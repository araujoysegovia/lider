<?php
namespace Lider\Bundle\LiderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DuelSimulatorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('lider:simulator')
            ->setDescription('simulador de duelos.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $listDuel = $em->getRepository("LiderBundle:Duel")->findBy(array("active" => true));
        foreach($listDuel as $duel)
        {
            echo "\n intresando en el duelo ".$duel->getId();
            $sw = true;
            while($sw)
            {
                $questions = $this->getQuestion($duel->getId(), $duel->getPlayerOne());
                if($questions)
                {
                    echo "\n\t haciendo pregunta al jugador ". $duel->getPlayerOne()->getId();
                    $this->simulator($questions, $duel, $duel->getPlayerOne());
                }
                $questions = $this->getQuestion($duel->getId(), $duel->getPlayerTwo());
                if($questions)
                {
                    echo "\n\t haciendo pregunta al jugador ". $duel->getPlayerTwo()->getId();
                    $r =$this->simulator($questions, $duel, $duel->getPlayerTwo());
                    if($r['lastOne'])
                    {
                        echo "\n\t Entre a la ultima";
                        $sw = false;
                    }
                }
                else{
                    $sw = false;
                }
            }
            
            
        }
    }

    private function simulator($question, $duel, $user)
    {
        $questionId = $question['question']['id'];
        $token = $question['token'];
        $total = count($question['question']['answers']);
        $pos = rand(0, $total-1);
        $answer = $question['question']['answers'][$pos]['id'];

        return $this->checkAnswerDuel($questionId, $answer, $token, $user);
    }

    private function getQuestion($duelId, $user)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $repository = $em->getRepository('LiderBundle:Duel');

        $duel = $repository->findOneBy(array('id' => $duelId));
        if(!$duel)
            throw new \Exception("Duel no found");

        $playerOne = $duel->getPlayerOne();
        $playerTwo = $duel->getPlayerTwo();

        if(($user->getId() == $playerOne->getId()) || ($user->getId() == $playerTwo->getId())){                
            
            $playerD = new \Lider\Bundle\LiderBundle\Document\Player();
            $playerD->getDataFromPlayerEntity($user);

            $question = $this->getContainer()->get('question_manager')->getQuestions(1, $duel, $user);

            if(count($question) == 0){
                $array = array(
                    'token' => null,
                    'question' => null
                ); 
                return false;
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

            $questionHistory = new \Lider\Bundle\LiderBundle\Document\QuestionHistory();
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

        return $array;
    }

    private function checkAnswerDuel($questionId, $answerId, $token, $user) {
        
        $em = $this->getContainer()->get('doctrine')->getManager();
        
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $questionHistory = $dm->getRepository("LiderBundle:QuestionHistory")
                     ->findOneBy(array("id" => $token));
        
        if(!$questionHistory){
            throw new \Exception("Question no found");
        }

        if($user->getId() != $questionHistory->getPlayer()->getPlayerId()){
            throw new \Exception("Question no found");
        }

        $now = new \DateTime();
       // $diffTime = $now->format('U') - $questionHistory->getEntryDate()->format('U');
        
        $parameters = $this->getContainer()->get('parameters_manager')->getParameters();
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

        if($isOk){

            $res['success'] = true;
            $res['code'] = '00';   /*Respuesta correcta*/

            $questionHistory->setFind(true);                
            $user->setWonGames($wonGames + 1);

            if(($questionHistory->getExtraQuestion() && $parameters['gamesParameters']['pointExtraDuel'] == 'true') || !$questionHistory->getExtraQuestion())
            {
                $this->applyPoints($questionHistory, $parameters, $team, $user, $duel);
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
            

        $questionHistory->setFinished(true);
        $dm->flush();
        $em->flush();

        $questionMissing = $this->getContainer()->get('question_manager')->getMissingQuestionFromDuel($duel, $user);
        $lastOne = false;

        if(count($questionMissing) == 0){
            $lastOne = true;
        }

        $res['lastOne'] = $lastOne;

        $gearman = $this->getContainer()->get('gearman');
        try{
            $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkerchequear~checkDuel', json_encode(array(
                        'duelId' => $duel->getId(),
                        'userId' => $user->getId()
                      )));
        }catch(\Exception $e){
            return $e;
        }
        return $res;
        
    }

    private function applyPoints(&$questionHistory, $parameters, $team, $user, &$duel)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        if($questionHistory->getUseHelp()){
            $pointsHelp = $parameters['gamesParameters']['questionPointsHelp'];
            $questionHistory->setPoints($pointsHelp);
            $playerPoint = new \Lider\Bundle\LiderBundle\Entity\PlayerPoint();
            $playerPoint->setPoints($pointsHelp);
            $playerPoint->setTournament($team->getTournament());
            $playerPoint->setTeam($team);
            $playerPoint->setPlayer($user);
            $this->applyPointsToDuel($duel, $user, $pointsHelp);
        }else{
            $points = $parameters['gamesParameters']['questionPoints'];
            $questionHistory->setPoints($points);
            $playerPoint = new \Lider\Bundle\LiderBundle\Entity\PlayerPoint();
            $playerPoint->setPoints($points);
            $playerPoint->setTournament($team->getTournament());
            $playerPoint->setTeam($team);
            $playerPoint->setPlayer($user);
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
}