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
    	$entity = $em->getRepository("LiderBundle:Tournament")->findBy(array("active" => true, "deleted" => false));
    	if(!$entity)
    		throw new \Exception("Entity no found");

    	return $this->get("talker")->response($entity);
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
