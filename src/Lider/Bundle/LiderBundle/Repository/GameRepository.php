<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class GameRepository extends MainRepository
{
	public function findGamesByGroup($tournament){
		$query = $this->createQueryBuilder('g')
						->select('g, ga, to, tt')
						->join('g.games', 'ga', 'WITH','ga.deleted = false')
						->join('ga.team_one', 'to', 'WITH','to.deleted = false')
						->join('ga.team_two', 'tt', 'WITH','tt.deleted = false')
						->where('g.deleted = false and g.tournament = :t')
						->setParameter('t', $tournament, \Doctrine\DBAL\Types\Type::INTEGER)
						->orderBy('g.name', 'ASC');
		
		$query = $query->getQuery();
		// echo $query->getSQL()."<br/><br/>";		
		return $query->getArrayResult();
	}
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


    public function getDuelsForGame($game)
    {
    	
    }

    public function findGameFromTwoTeams($team1, $team2, $tournamentId)
    {
    	$query = $this->createQueryBuilder('g')
    	->select('g, to')
    	->leftJoin('g.tournament', 'to', 'WITH', 'to.deleted = FALSE')
    	->where('g.deleted = FALSE AND ((g.team_one = :o AND g.team_two = :t) OR (g.team_one = :t AND g.team_two = :o)) AND to.id = :f')
    	->setParameter('o', $team1)
    	->setParameter('t', $team2)
    	->setParameter('f', $tournamentId);

    	$query = $query->getQuery();
    	$r = $query->getResult();

    	return $r;
    }
}