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
    
    
    /**
     *  Obtener Historial de duelos actual del usuario en session
     */
    public function getHistoryDuelAction(){
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$duel = $em->getRepository("LiderBundle:Duel")->findHistoryPlayerDuel($user);
    	if(!$duel)
    		return $this->get("talker")->response(array());
    	else
    		return $this->get("talker")->response($duel);
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
