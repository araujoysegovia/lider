<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lider\Bundle\LiderBundle\Document\QuestionHistory;
use Lider\Bundle\LiderBundle\Document\Image;
use Lider\Bundle\LiderBundle\Document\ReportQuestion;
use Lider\Bundle\LiderBundle\Entity\PlayerPoint;

class QuestionController extends Controller
{
    private $maxSec = 45;

    public function getName(){
        return "Question";
    }
    
    /**
     * Verificar pregunta (Administrador)
     * @param $id
     * @throws \Exception
     */
    public function checkAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $id, "deleted" => false));
        if(!$entity)
            throw new \Exception("Entity no found");
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        $entity->setUser($user);
        $entity->setChecked(true);
        
        $em->flush();
        
        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
        
    }
    
    /**
     * Obtener pregunta de practica
     */
    public function getTestQuestionAction() {
        
        $em = $this->getDoctrine()->getEntityManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
                
        $question = $this->get("question_manager")->getQuestions(1);
        if(!$question)
            throw new \Exception("Pregunta no encontrada", 500);

        $question = $question[0];
            
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $playerD = new \Lider\Bundle\LiderBundle\Document\Player();
        $playerD->getDataFromPlayerEntity($user);

        $q = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $question['id'], "deleted" => false));
        if(!$q)
            throw new \Exception("Entity no found");

        $questionD = new \Lider\Bundle\LiderBundle\Document\Question();
        $questionD->getDataFromQuestionEntity($q);
        $questionHistory = new QuestionHistory();
        $questionHistory->setPlayer($playerD);    
        $questionHistory->setQuestion($questionD);
        $questionHistory->setDuel(false);
      
        $questionHistory->setEntryDate(new \MongoDate());

         foreach ($q->getAnswers()->toArray() as $key => $value) {
      
            $ansD = new \Lider\Bundle\LiderBundle\Document\Answer();
            $ansD->getDataFromAnswerEntity($value);

            $questionHistory->addAnswer($ansD);
         }
      
        
        $dm->persist($questionHistory);
        $dm->flush();
        
        $array = array(
            'token' => $questionHistory->getId(),
            'question' => $question
        );
        
        return $this->get("talker")->response($array);
    }
    
    /** 
     * Verificar la respuesta enviada
     */
    public function checkAnswerAction() {
    	//throw new \Exception("No data");
        
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->get("request");
        $data = $request->getContent();
        
        if(empty($data) || !$data)
            throw new \Exception("No data");
        
        $data = json_decode($data, true);
        
        $questionId = $data['questionId'];
        $answerId = $data['answerId'];
        $token = $data['token'];
        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $entity = $dm->getRepository("LiderBundle:QuestionHistory")
                     ->findOneBy(array("id" => $token));
        
        if(!$entity){
            throw new \Exception("Question no found");
        }
        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user->getId() != $entity->getPlayer()->getPlayerId()){
            throw new \Exception("Question no found");
        }

        $now = new \DateTime();
        $diffTime = $now->format('U') - $entity->getEntryDate()->format('U');
        
        $parameters = $this->get('parameters_manager')->getParameters();

        $question = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" =>$questionId, "deleted" => false));
        if(empty($question))
            throw new \Exception("No entity found");  

        $isOk = false;

        foreach ($question->getAnswers()->toArray() as $value) {
            if($value->getSelected()) {
                $answerD = new \Lider\Bundle\LiderBundle\Document\Answer();
                $answerD->getDataFromAnswerEntity($value);                  
                $entity->setAnswerOk($answerD);
                if($answerId == $value->getId()){
                    $isOk = true;
                    break;
                }                   
            }
        }        

        $parameters = $this->get('parameters_manager')->getParameters();
        $maxSec = $parameters['gamesParameters']['timeQuestionPractice'];

        if($diffTime >= $maxSec || $questionId=="no-answer"){
            $res = array();
            $res['success'] = false;
            $res['code'] = '01';  /*Tiempo agotado*/
            // if($parameters['gamesParameters']['answerShowPractice']){
            //     $res['answerOk'] = $entity->getAnswerOk()->getAnswerId();
            // }
            $entity->setTimeOut(true);
        }else{
        
            if($isOk){
                $res['success'] = true;
                $res['code'] = '00';
                $entity->setFind(true);
            }else{
                $res = array();
                $res['success'] = false;
                $res['code'] = '02';   /*Respuesta errada*/

                if($parameters['gamesParameters']['answerShowPractice'] == 'true'){
                    $res['answerOk'] = $entity->getAnswerOk()->getAnswerId();
                }
            }
            

            $answerSelected = $em->getRepository("LiderBundle:Answer")->findOneBy(array("id" =>$answerId, "deleted" => false));
            if(empty($answerSelected))
                throw new \Exception("No entity found");  

            $answerSelectedD = new \Lider\Bundle\LiderBundle\Document\Answer();
                        $answerSelectedD->getDataFromAnswerEntity($answerSelected);
               
            $entity->setSelectedAnswer($answerSelectedD);       
            
        }
                
        $entity->setFinished(true);
        $dm->flush();
        
        return $this->get("talker")->response($res);
        
    }

    public function countQuestionForGameAction($gameId)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('LiderBundle:Game');
        $game = $repository->findOneBy(array('id' => $gameId));
        if(!$game)
            throw new \Exception("Game Not Found");
        $duels = $game->getDuels();
        $return = array();
        foreach($duels as $duel)
        {
            $playerOne = $duel->getPlayerOne();
            $playerTwo = $duel->getPlayerTwo();
            $questionPlayerOne = $this->get('question_manager')->getMissingQuestionFromDuel($duel, $playerOne);
            $questionPlayerTwo = $this->get('question_manager')->getMissingQuestionFromDuel($duel, $playerTwo);
            $return[$duel->getId()]['playerOne'] = count($questionPlayerOne);
            $return[$duel->getId()]['playerTwo'] = count($questionPlayerTwo);
        }
        
        return $this->get("talker")->response(array("total" => count($return), "data" => $return));
    }

    public function countQuestionFromDuelAction($duelId)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('LiderBundle:Duel');
        $duel = $repository->findOneBy(array('id' => $duelId));
        if(!$duel)
            throw new \Exception("Duel Not Found");

        $question = $this->get('question_manager')->getQuestions(1, $duel);
        return $this->get("talker")->response(array("total" => count($question)));
    }
    
    public function getQuestionAction($duelId)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repository = $em->getRepository('LiderBundle:Duel');

        $duel = $repository->findOneBy(array('id' => $duelId));
        if(!$duel)
            throw new \Exception("Duel no found");

        $playerOne = $duel->getPlayerOne();
        $playerTwo = $duel->getPlayerTwo();

        $user = $this->container->get('security.context')->getToken()->getUser();

        if(($user->getId() == $playerOne->getId()) || ($user->getId() == $playerTwo->getId())){                
            
            $playerD = new \Lider\Bundle\LiderBundle\Document\Player();
            $playerD->getDataFromPlayerEntity($user);

            $question = $this->get('question_manager')->getQuestions(1, $duel);

            if(count($question) == 0){
                $array = array(
                    'token' => null,
                    'question' => null
                ); 
                return $this->get("talker")->response($array);
            }

            $q = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $question[0]['id'], "deleted" => false));
            if(!$q)
                throw new \Exception("Entity no found");
            
            $questionD = new \Lider\Bundle\LiderBundle\Document\Question();
            $questionD->getDataFromQuestionEntity($q);

            $tourmanetD = new \Lider\Bundle\LiderBundle\Document\Tournament();
            $tourmanetD->getDataFromTournamentEntity($duel->getTournament());

            $groupD = new \Lider\Bundle\LiderBundle\Document\Group();
            $groupD->getDataFromGroupEntity($user->getTeam()->getGroup());

            $teamD = new \Lider\Bundle\LiderBundle\Document\Team();
            $teamD->getDataFromTeamEntity($user->getTeam());

            $questionHistory = new QuestionHistory();
            $questionHistory->setPlayer($playerD);    
            $questionHistory->setQuestion($questionD);
            $questionHistory->setDuel(true);
            $questionHistory->setEntryDate(new \MongoDate());
            $questionHistory->setDuelId($duel->getId());
            $questionHistory->setFinished(false);
            $questionHistory->setTournament($tourmanetD);  
            $questionHistory->setTeam($teamD);  
            $questionHistory->setGroup($groupD);
            $questionHistory->setGameId($duel->getGame()->getId());
            if($duel->getExtraDuel())
            {
                $questionHistory->setExtraQuestion(true);
            }

            foreach ($q->getAnswers()->toArray() as $key => $value) {
          
                $ansD = new \Lider\Bundle\LiderBundle\Document\Answer();
                $ansD->getDataFromAnswerEntity($value);

                $questionHistory->addAnswer($ansD);
            }                        

            $dm->persist($questionHistory);
            $dm->flush();
         
            $array = array(
                'token' => $questionHistory->getId(),
                'question' => $question[0]                
            ); 
               
        }

        return $this->get("talker")->response($array);
    }


    public function checkAnswerDuelAction() {
        
        
        $em = $this->getDoctrine()->getEntityManager();        
        $request = $this->get("request");
        $data = $request->getContent();
        
        if(empty($data) || !$data)
            throw new \Exception("No data");
        
        $data = json_decode($data, true);        

        $questionId = $data['questionId'];
        $answerId = $data['answerId'];
        $token = $data['token'];
        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $questionHistory = $dm->getRepository("LiderBundle:QuestionHistory")
                     ->findOneBy(array("id" => $token));
        
        if(!$questionHistory){
            throw new \Exception("Question no found");
        }

        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user->getId() != $questionHistory->getPlayer()->getPlayerId()){
            throw new \Exception("Question no found");
        }

        $now = new \DateTime();
        $diffTime = $now->format('U') - $questionHistory->getEntryDate()->format('U');
        
        $parameters = $this->get('parameters_manager')->getParameters();
        $maxSec = $parameters['gamesParameters']['timeQuestionDuel'];

        $question = $em->getRepository("LiderBundle:Question")
                       ->findOneBy(array("id" =>$questionId, "deleted" => false));

        if(empty($question))
            throw new \Exception("No entity found");  

        $isOk = false;

        foreach ($question->getAnswers()->toArray() as $value) {
            if($value->getSelected()) {
                $answerD = new \Lider\Bundle\LiderBundle\Document\Answer();
                $answerD->getDataFromAnswerEntity($value);                  
                $questionHistory->setAnswerOk($answerD);
                if($answerId == $value->getId()){
                    $isOk = true;
                    break;
                }                   
            }
        }        

        $wonGames = $user->getWonGames();
        $lostGames = $user->getLostGames();
        $playerPoints = $user->getPlayerPoints();
        
        $team = $user->getTeam();
        $duel = $em->getRepository('LiderBundle:Duel')->find($questionHistory->getDuelId());

        if($diffTime >= $maxSec || $questionId=="no-answer"){
            $res = array();
            $res['success'] = false;
            $res['code'] = '01';  /*Tiempo agotado*/

            $questionHistory->setTimeOut(true);
            $user->setLostGames($lostGames + 1);

        }else{
        
            if($isOk){

                $res['success'] = true;
                $res['code'] = '00';   /*Respuesta correcta*/

                $questionHistory->setFind(true);                
                $user->setWonGames($wonGames + 1);

                if(($questionHistory->getExtraQuestion() && $parameters['gamesParameters']['pointExtraDuel'] == 'true') || !$questionHistory->getExtraQuestion())
                {
                    $this->applyPoints($questionHistory, $parameters, $team, $user, $duel, $question);
                }

            }else{
                $res = array();
                $res['success'] = false;
                $res['code'] = '02';   /*Respuesta errada*/

                if($parameters['gamesParameters']['answerShowPractice'] == 'true'){
                    $res['answerOk'] = $questionHistory->getAnswerOk()->getAnswerId();
                }

                $user->setLostGames($lostGames + 1);
            }
            

            $answerSelected = $em->getRepository("LiderBundle:Answer")->findOneBy(array("id" =>$answerId, "deleted" => false));
            if(empty($answerSelected))
                throw new \Exception("No entity found");  

            $answerSelectedD = new \Lider\Bundle\LiderBundle\Document\Answer();
            $answerSelectedD->getDataFromAnswerEntity($answerSelected);
               
            $questionHistory->setSelectedAnswer($answerSelectedD);       
            
        }
                
        $user = $this->get('security.context')->getToken()->getUser();
        

        $questionHistory->setFinished(true);
        $dm->flush();
        $em->flush();


        $questionMissing = $this->get('question_manager')->getMissingQuestionFromDuel($duel, $user);
        $lastOne = false;  

        if(count($questionMissing) == 0){
            $lastOne = true;
        }

        $res['lastOne'] = $lastOne;

        $gearman = $this->get('gearman');
        
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkerchequear~checkDuel', json_encode(array(
                     'duelId' => $duel->getId(),
                     'userId' => $user->getId()
                  )));
                        
       	return $this->get("talker")->response($res);
        
    }

    private function applyPoints(&$questionHistory, $parameters, $team, $user, &$duel, $question)
    {
        $em = $this->getDoctrine()->getManager();
        if($questionHistory->getUseHelp()){
            $pointsHelp = $parameters['gamesParameters']['questionPointsHelp'];
            $questionHistory->setPoints($pointsHelp);
            $playerPoint = new PlayerPoint();                   
            $playerPoint->setPoints($pointsHelp);
            $playerPoint->setTournament($team->getTournament());
            $playerPoint->setTeam($team);
            $playerPoint->setPlayer($user);
            $playerPoint->setDuel($duel);
            $playerPoint->setQuestion($question);
            $this->applyPointsToDuel($duel, $user, $pointsHelp);
        }else{
            $points = $parameters['gamesParameters']['questionPoints'];
            $questionHistory->setPoints($points);
            $playerPoint = new PlayerPoint();                   
            $playerPoint->setPoints($points);
            $playerPoint->setTournament($team->getTournament());
            $playerPoint->setTeam($team);
            $playerPoint->setPlayer($user);
            $playerPoint->setDuel($duel);
            $playerPoint->setQuestion($question);
            $this->applyPointsToDuel($duel, $user, $points);
        }
        $em->persist($playerPoint);
        $user->addPlayerPoint($playerPoint);
    }

	private function applyPointsToDuel(&$duel, $user, $points)
    {
        if($user->getId() == $duel->getPlayerOne()->getId())
        {
            $duel->setPointOne($duel->getPointOne()+$points);
        }
        else{
            $duel->setPointTwo($duel->getPointTwo()+$points);
        }
    }

    public function setHelpAction($token)
    {        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $questionHistory = $dm->getRepository("LiderBundle:QuestionHistory")
                              ->findOneBy(array("id" => $token));       

        $questionHistory->setUseHelp(true);

        $dm->flush();

        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }

    /** 
     * Setear una imagen a una pregunta y guardarla en la BD mongo
     * @param unknown $id
     * @throws \Exception
     */
    public function setImageAction($id) {

        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $id, "deleted" => false));
        if(!$entity)
            throw new \Exception("Entity no found");
         
        $dm = $this->get('doctrine_mongodb')->getManager();
        $request = $this->get("request");      

        $uploadedFile = $request->files->get('imagen');
        $className = self::$NAMESPACE.$this->getName();
         
        $image = new Image();
        $image->setName($uploadedFile->getClientOriginalName());
        $image->setFile($uploadedFile->getPathname());
        $image->setMimetype($uploadedFile->getClientMimeType());
        $image->setEntity($className);
        $image->setEntityId($id);
    
        $dm->persist($image);
        $dm->flush();
         
        $entity->setImage($image->getId());
        $entity->setHasImage(true);
        
        $em->flush();
         
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }    
    
    /** 
     * Remover la imagen asignada a una pregunta
     * @param $id
     * @throws \Exception
     */
    public function removeImageAction($id) {
            
        $em = $this->getDoctrine()->getEntityManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
        
        $question = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $id, "deleted" => false));
        if(!$question)
            throw new \Exception("Entity no found");

        $image = $dm->getRepository("LiderBundle:Image")->findOneBy(array("id" => $question->getImage(), "deleted" => false));
        if(!$image)
            throw new \Exception("Document no found");
         
        $dm->remove($image);        
        
        $question->setImage(null);
        $question->setHasImage(false);
                
        $em->flush();
        $dm->flush();
        
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }    

    /**
     * Reportar un error en la pregunta
     */
    public function questionReportAction() {
        
        $em = $this->getDoctrine()->getEntityManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $gearman = $this->get('gearman');
        
        $request = $this->get("request");
        $data = $request->getContent();
         
        if(empty($data) || !$data)
            throw new \Exception("No data");
         
        $data = json_decode($data, true);
         
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $questionId = $data['questionId'];
        $playerId = $user->getId();
        $reportText = $data['reportText'];        
        $causal = $data['causal']; 
        
        $playerD = new \Lider\Bundle\LiderBundle\Document\Player();
        $playerD->getDataFromPlayerEntity($user);
        
        $q = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $questionId, "deleted" => false));
        if(!$q)
            throw new \Exception("Entity no found");

        $questionD = new \Lider\Bundle\LiderBundle\Document\Question();
        $questionD->getDataFromQuestionEntity($q);

        $reportQuestion = new ReportQuestion();
        $reportQuestion->setQuestion($questionD);
        $reportQuestion->setPlayer($playerD);
        $reportQuestion->setReportText($reportText);
        $reportQuestion->setReportDate(new \MongoDate());
        $reportQuestion->setCausal($causal);
        
        $dm->persist($reportQuestion);
        $dm->flush();
        $body = '<p>'.$questionD->getQuestionId().' - '.$questionD->getQuestion().'</p><ul>';
        foreach($q->getAnswers()->toArray() as $value){
            $body .= '<li>'.$value->getAnswer().'</li>';
        }
        $body .= '</ul><br><br><h3>CAUSAL:</h3><p>'.$causal.'</p>';             
        try{               
            $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotification', json_encode(array(
                'subject' => 'Nuevo reporte de pregunta',
                'templateData' => array(
                    'title' => 'Nuevo Reporte',
                    'user' => array(
                        'image' => $user->getImage(),
                        'name' => $user->getName(),
                        'lastname' => $user->getLastname()
                    ),
                    'subjectUser' => $reportText,
                    'body' => $body
                )
                
            )));

        }catch(\Exception $e){
            echo "error";
        }
        
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }
    
    public function updatePointsToDuelAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$list = $dm->getRepository("LiderBundle:QuestionHistory")->getPointsByDuel();
    	$duels = $em->getRepository("LiderBundle:Duel")->findAll();
    	$c = 0;
    	foreach($duels as $duel)
    	{
    		foreach($list as $d)
    		{
    
    			if($d['duelId'] == $duel->getId())
    			{
    				$c++;
    				 
    				echo $c . " - ".$d['duelId'] . " == ". $duel->getId(). "\n";
    				
    				if($d['player.playerId'] == $duel->getPlayerOne()->getId())
    				{
    					$duel->setPointOne($d['total']);
    				}
    				else{
    					$duel->setPointTwo($d['total']);
    				}
    			}
    		}
    	}
    	$em->flush();
    	return $this->get("talker")->response($list->toArray());
    }

    public function reverseQuestionAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $questionHistoryRepo = $dm->getRepository("LiderBundle:QuestionHistory");
        $playerRepository = $em->getRepository("LiderBundle:Player");
        $duelRepository = $em->getRepository("LiderBundle:Duel");
        $playerPointRepository = $em->getRepository("LiderBundle:PlayerPoint");
        $gameRepository = $em->getRepository("LiderBundle:Game");
        $question = $questionHistoryRepo->find($token);
        if($question)
        {
            $player = $playerRepository->find($question->getPlayer()->getPlayerId());
            $duel = $duelRepository->find($question->getDuelId());
            if($question->getFind())
            {
                $player->setWonGames($player->getWonGames() - 1);
                if($duel->getPlayerOne()->getId() == $player->getId())
                {
                    $duel->setPointOne($duel->getPointOne() - $question->getPoints());
                }
                elseif($duel->getPlayerTwo()->getId() == $player->getId())
                {
                    $duel->setPointTwo($duel->getPointTwo() - $question->getPoints());
                } 
                $playerPoint = $playerPointRepository->findOneBy(array("player" => $player->getId(), "points" => $question->getPoints(), "duel" => $duel->getId(), "question" => $question->getQuestion()->getQuestionId()));
                $em->remove($playerPoint);
            }
            else{
                $player->setLostGames($player->getLostGames() - 1);
            }
            if(!$duel->getActive() && $duel->getFinished())
            {
                $game = $duel->getGame();
                $duel->setActive(true);
                $duel->setFinished(false);
                $duel->setPlayerWin(null);
                if(!$game->getActive() && $game->getFinished())
                {
                    $game->setActive(true);
                    $game->setFinished(false);
                    
                    if($game->getTeamOne()->getId() == $game->getTeamWinner()->getId())
                    {
                        $game->setPointOne(0);
                    }
                    elseif($game->getTeamTwo()->getId() == $game->getTeamWinner()->getId())
                    {
                        $game->setPointTwo(0);
                    }
                    $game->setTeamWinner(null);
                }
            }
            $em->flush();
            $dm->remove($question);
            $dm->flush();
        }
        return $this->get("talker")->response($this->getAnswer(true, $this->delete_successful));
    }
}
