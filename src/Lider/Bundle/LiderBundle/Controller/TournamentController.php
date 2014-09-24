<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TournamentController extends Controller
{
    public function getName(){
    	return "Tournament";
    }

    /**
     * Buscar torneos activos
     */
    public function activeTournamentAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$tournament = $em->getRepository("LiderBundle:Tournament")->findBy(array("active" => true, "deleted" => false));
//     	$entity = $em->getRepository("LiderBundle:Tournament")->activeTournament();
    	if(!$tournament)
    		throw new \Exception("Entity no found");

    	$teams = $em->getRepository("LiderBundle:Team")->findBy(array("tournament" => $tournament, "deleted" => false));
    	
    	return $this->get("talker")->response($teams);
    }

    public function getOnlyTournamentsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository("LiderBundle:Tournament")->findBy(array());
        if(!$entity)
            throw new \Exception("Entity no found");

        return $this->get("talker")->response($entity);
    }
}
