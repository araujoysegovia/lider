<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class GameRepository extends MainRepository
{
    public function getExpiredGame($date)
    {
    	$query =  $this->createQueryBuilder('g')
						->select('g, d')
						->leftJoin('g.duels', 'd', 'WITH', 'd.deleted = false')		
						->where('g.deleted = false AND g.enddate < :date AND g.active = true AND g.finished = false')
						->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATETIME);

		$query = $query->getQuery();
		$r = $query->getResult();
		
		return $r;
    }


    public function getGamesToStart($date)
    {
    	$query =  $this->createQueryBuilder('g')
						->select('g, d')
						->leftJoin('g.duels', 'd', 'WITH', 'd.deleted = false')		
						->where('g.deleted = false AND g.startdate <= :date AND g.finished = false AND g.active = false')
						->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATETIME);

		$query = $query->getQuery();
		$r = $query->getResult();
		
		return $r;
    }
}