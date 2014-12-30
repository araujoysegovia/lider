<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DuelController extends Controller
{
    public function getName(){
    	return "Duel";
    }
    
    /**
     * Obtener duelo actual del usuario en session
     */
    public function getCurrentDuelAction(){
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$duel = $em->getRepository("LiderBundle:Duel")->findCurrentPlayerDuel($user);
    	if(!$duel)
    		return $this->get("talker")->response(array());
    	else 
    		return $this->get("talker")->response(array('total'=>count($duel), 'data'=>$duel));
    }

    public function getDuelsByGameAction($gameId)
    {
        $em = $this->getDoctrine()->getManager();
        $duels = $em->getRepository('LiderBundle:Duel')->getDuelsByGame($gameId);
        $data = array("total" => 0, "data" => array());
        if(!$duels){
            return $this->get("talker")->response($data);
        }
        else{
            $data['total'] = count($duels);
            $questionManager = $this->get('question_manager');
            foreach($duels as $key =>  $duelEntity)
            {
                $duel = $this->get("talker")->normalizeEntity($duelEntity);

                $playerOne = $duelEntity->getPlayerOne();
                $playerTwo = $duelEntity->getPlayerTwo();
                // $d = $em->getRepository("LiderBundle:Duel")->find($duelEntity->getId());
                $questionPlayerOne = $questionManager->getMissingQuestionFromDuel($duelEntity, $playerOne);
                $questionPlayerTwo = $questionManager->getMissingQuestionFromDuel($duelEntity, $playerTwo);

                $duel['player_one']['questionMissing'] = count($questionPlayerOne);
                $duel['player_one']['teamId'] = $playerOne->getTeam()->getId();
                $duel['player_two']['questionMissing'] = count($questionPlayerTwo);
                $duel['player_two']['teamId'] = $playerTwo->getTeam()->getId();
                $data['data'][$key] = $duel;
            }
            // return $this->get("talker")->response(array());
            return $this->get("talker")->response($data);
        }
    }


    /**
     *  Obtener Historial de duelos actual del usuario en session
     */
    public function getHistoryDuelAction(){
    	$em = $this->getDoctrine()->getEntityManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$duels = $em->getRepository("LiderBundle:Duel")->findHistoryPlayerDuel($user);
        // print_r($duel);
    	if(!$duels){
    		return $this->get("talker")->response(array());
        }
    	else{

            $qhr = $dm->getRepository("LiderBundle:QuestionHistory");
            $return = array();
            foreach($duels as $duel)
            {
                $points = $qhr->getQuestionForPlayerByDuel($duel['id']);
                if($user->getId() == $duel['player_one']['id'])
                {
                    $playervs = $duel['player_two'];
                }
                else{
                    $playervs = $duel['player_one'];
                }
                $return[$playervs['id']]['nameVs'] =  $playervs['name'].' '.$playervs['lastname'];
                $return[$playervs['id']]['emailVs'] =  $playervs['email'];
                $return[$playervs['id']]['imageVs'] =  $playervs['image'];
                $return[$playervs['id']]['total'] =  $points[0]['total'];
                if($points[0]['player.playerId'] == $user->getId())
                {
                    $return[$playervs['id']]['you'] = $points[0]['win'];
                    $return[$playervs['id']]['vs'] = $points[1]['win'];
                }
                else
                {
                    $return[$playervs['id']]['you'] = $points[1]['win'];
                    $return[$playervs['id']]['vs'] = $points[0]['win'];
                }
            }
            
    		return $this->get("talker")->response(array("count" => count($duel), "data" => $return));
        }
    }
    
    public function getQuestionsFromDuelAction($duelId)
    {
    	$em = $this->getDoctrine()->getManager();
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$duelQuestionRepo = $em->getRepository("LiderBundle:DuelQuestion");
    	$listQuestion = $duelQuestionRepo->findBy(array("duel" => $duelId));
    	$questionHistoryRepo = $dm->getRepository("LiderBundle:QuestionHistory");
    	$listMongoQuestion = $questionHistoryRepo->findBy(array("duelId" => intval($duelId)));
    	if(!$listQuestion)
    	{
    		throw new \Exception("No hay preguntas generadas");
    	}
    	$sw = false;
    	$questionArray = array();
    	foreach($listQuestion as $question)
    	{
    		if(!$sw)
    		{
    			$duel = $question->getDuel();
    			$questionArray['duelId'] = $duelId;
    			$questionArray['playerOne'] = array(
    				'id' => $duel->getPlayerOne()->getId(),
    				'name' => $duel->getPlayerOne()->getName()." ".$duel->getPlayerOne()->getLastname(),
                    'duel' => false,
    				'image' => $duel->getPlayerOne()->getImage()
    			);
    			$questionArray['playerTwo'] = array(
    					'id' => $duel->getPlayerTwo()->getId(),
    					'name' => $duel->getPlayerTwo()->getName()." ".$duel->getPlayerTwo()->getLastname(),
                        'duel' => false,
    					'image' => $duel->getPlayerTwo()->getImage()
    			);
    			$questionArray['questions'] = array();
    			$sw = true;
    		}
            $q = array(
    			'question' => $question->getQuestion()->getQuestion(),
    			'questionId' => $question->getQuestion()->getId(),
    			'answers' => array(
                    'playerOne' => array(),
                    'playerTwo' => array(),
                )
            );
    		foreach($listMongoQuestion as $mongoQuestion)
    		{
                if($mongoQuestion->getQuestion()->getQuestionId() == $question->getQuestion()->getId())
                {
                    // echo $mongoQuestion->getPlayer()->getPlayerId(). " = " . $questionArray['playerOne']['id'];
                    if($mongoQuestion->getPlayer()->getPlayerId() == $questionArray['playerOne']['id'] && $mongoQuestion->getSelectedAnswer())
                    {
                        $questionArray['playerOne']['duel'] = true;
                        $q['answers']['playerOne'] = array(
                            'token' => $mongoQuestion->getId(),
                            'answerId' => $mongoQuestion->getSelectedAnswer()->getAnswerId(),
                            'answer' => $mongoQuestion->getSelectedAnswer()->getAnswer(),
                            'find' => $mongoQuestion->getFind()
                        );
                    }
                    elseif($mongoQuestion->getPlayer()->getPlayerId() == $questionArray['playerOne']['id'])
                    {
                        $questionArray['playerOne']['duel'] = true;
                        $q['answers']['playerOne'] = array(
                            'token' => $mongoQuestion->getId(),
                            'answerId' => '',
                            'answer' => '',
                            'find' => false
                        );
                    }
                    if($mongoQuestion->getPlayer()->getPlayerId() == $questionArray['playerTwo']['id'] && $mongoQuestion->getSelectedAnswer())
                    {
                        $questionArray['playerTwo']['duel'] = true;
                        $q['answers']['playerTwo'] = array(
                            'token' => $mongoQuestion->getId(),
                            'answerId' => $mongoQuestion->getSelectedAnswer()->getAnswerId(),
                            'answer' => $mongoQuestion->getSelectedAnswer()->getAnswer(),
                            'find' => $mongoQuestion->getFind()
                        );
                    }
                    elseif($mongoQuestion->getPlayer()->getPlayerId() == $questionArray['playerTwo']['id'])
                    {
                        $questionArray['playerTwo']['duel'] = true;
                        $q['answers']['playerTwo'] = array(
                            'token' => $mongoQuestion->getId(),
                            'answerId' => '',
                            'answer' => '',
                            'find' => false
                        );
                    }
                }
    		}
            $questionArray['questions'][] = $q;
            // break;
    	}
    	return $this->get("talker")->response($questionArray);
    }
 

    public function getDuelAction($duelId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.context')->getToken()->getUser();
        $duels = $em->getRepository("LiderBundle:Duel")->findCurrentPlayerDuel($user);

        $d = null;
        foreach ($duels as $key => $duel) {
            if($duel['id'] == $duelId){
                $d = $duel;
                break;
            }
        }
        if(!$d){
            throw new \Exception("El duelo no pertenece al usuario", 1);            
        }

       return $this->get("talker")->response($d);
        
    }

    public function notificationByDuelAction($duelId)
    {
        $gearman = $this->get('gearman');
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkerchequear~sendNotificationPlayersDuel', json_encode(array(
                'duelId' => $duelId
        )));
        
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));      
    }

    /**
     * Funcion para cerrar el duelo de manera manual
     */
    public function closeDuelManualAction($duelId)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('LiderBundle:Duel');
        if(!$duelId)
        {
            throw new \Exception("Id del duelo necesario", 1);
        }
        $duel = $repo->find($duelId);
        if($duel->getPointOne() > $duel->getPointTwo())
        {
            $duel->setPlayerWin($duel->getPlayerOne());
        }
        elseif($duel->getPointOne() < $duel->getPointTwo())
        {
            $duel->setPlayerWin($duel->getPlayerTwo());
        }
        $duel->setActive(false);
        $duel->setFinished(true);
        $em->flush();
    }
}
