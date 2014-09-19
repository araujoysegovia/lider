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

    public function getTournament($id)
    {
    	$query =  $this->createQueryBuilder('to')
						->select('to, te, g')
						->join('to.groups', 'g', 'WITH', 'g.deleted = false')						
						->join('g.teams', 'te', 'WITH', 'te.deleted = false')						
						->where('to.deleted = false AND to.id = :id')
						->setParameter('id', $id);

		$query = $query->getQuery();
		$r = $query->getSingleResult();

		// print_r($r);
		return $r;
    }
}