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
        $duels = $em->getRepository('LiderBundle:Duel')->getArrayEntityWithOneLevel(array("game" => $gameId, 'deleted' => false));
        if(!$duels){
            return $this->get("talker")->response(array());
        }
        else{
            $questionManager = $this->get('question_manager');
            foreach($duels['data'] as $key =>  $duel)
            {
                $playerOne = $em->getRepository("LiderBundle:Player")->find($duel['player_one']['id']);
                $playerTwo = $em->getRepository("LiderBundle:Player")->find($duel['player_two']['id']);
                $d = $em->getRepository("LiderBundle:Duel")->find($duel['id']);
                $questionPlayerOne = $questionManager->getMissingQuestionFromDuel($d, $playerOne);
                $questionPlayerTwo = $questionManager->getMissingQuestionFromDuel($d, $playerTwo);
                $duel['player_one']['questionMissing'] = count($questionPlayerOne);
                $duel['player_one']['teamId'] = $playerOne->getTeam()->getId();
                $duel['player_two']['questionMissing'] = count($questionPlayerTwo);
                $duel['player_two']['teamId'] = $playerTwo->getTeam()->getId();
                $duels['data'][$key] = $duel;
            }
            return $this->get("talker")->response($duels);
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
 

    public function getDuelAction($duelId)
    {
        $em = $this->getDoctrine()->getEntityManager();

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
}
