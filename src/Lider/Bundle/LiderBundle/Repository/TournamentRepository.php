<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class TournamentRepository extends MainRepository
{
	
    public function activeTournament()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository("LiderBundle:Tournament")->findOneBy(array("active" => true, "deleted" => false));
    	if(!$entity)
    		throw new \Exception("Entity no found");

    	
    }
}