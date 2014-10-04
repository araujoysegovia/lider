<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class TournamentRepository extends MainRepository
{
	
    public function activeTournament()
    {    
    	$em = $this->getEntityManager();
    	$query =  $em->createQuery('SELECT t, to FROM LiderBundle:Team t
            JOIN t.tournament to
            WHERE t.deleted = false AND to.active = true AND to.deleted = false');  	
    	
    	//$query->getResult();
    	
    	//$query = $query->getQuery();
    	$r = $query->getArrayResult();

    	return $r;
    }

    public function getTournament($id)
    {
    	$query =  $this->createQueryBuilder('to')
						->select('to')
						// ->leftJoin('to.groups', 'g', 'WITH', 'g.deleted = false')						
						// ->leftJoin('g.teams', 'te', 'WITH', 'te.deleted = false')
      //                   ->leftJoin('to.games', 'ga', 'WITH', 'ga.deleted = false')
      //                   ->leftJoin('ga.duels', 'd', 'WITH', 'd.deleted = false')
						->where('to.deleted = false AND to.id = :id')
						->setParameter('id', $id);

		$query = $query->getQuery();
		$r = $query->getSingleResult();

		// print_r($r);
		return $r;
    }
}