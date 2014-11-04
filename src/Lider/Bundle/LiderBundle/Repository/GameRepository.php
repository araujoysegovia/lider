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
						->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATE);

		$query = $query->getQuery();
		$r = $query->getResult();
		
		return $r;
    }

    public function getGamesNoHaveActiveDuels()
    {
    	$query =  $this->createQueryBuilder('g')
						->select('g')
						->leftJoin('g.duels', 'd', 'WITH', 'd.deleted = false')		
						->where('g.deleted = false AND g.active = true AND g.finished = false and d.active = false AND d.finished = true');

		$query = $query->getQuery();
		$r = $query->getArrayResult();
		
		return $r;
    }

    public function findGameFromTwoTeams($team1, $team2, $tournamentId)
    {
    	$query = $this->createQueryBuilder('g')
    	->select('g, to, ton, ttw')
    	->join('g.tournament', 'to', 'WITH', 'to.deleted = FALSE')
    	->join('g.team_one', 'ton', 'WITH', 'ton.deleted = FALSE')
    	->join('g.team_two', 'ttw', 'WITH', 'ttw.deleted = FALSE')
    	->where('g.deleted = FALSE AND ((g.team_one = :o AND g.team_two = :t) OR (g.team_one = :t AND g.team_two = :o)) AND to.id = :f')
    	->setParameter('o', $team1)
    	->setParameter('t', $team2)
    	->setParameter('f', $tournamentId);

    	$query = $query->getQuery();
    	$r = $query->getArrayResult();

    	return $r;
    }

    public function getGamesByTeam($team){
       	$query = $this->createQueryBuilder('g')
    		->select('g, po, two, win')
	    	->join('g.team_one', 'po')
	    	->join('g.team_two', 'two')
	    	->leftJoin('g.team_winner', 'win')
	    	->where('g.finished = true and g.deleted=false and g.active = false and (po.id = :team or two.id = :team or win.id = :team) and g.level = :l')
	    	->setParameter('team', $team, \Doctrine\DBAL\Types\Type::INTEGER)
	    	->setParameter('l', 1, \Doctrine\DBAL\Types\Type::INTEGER);


		$query = $query->getQuery();
    	$r = $query->getArrayResult();
		return $r;
	}

	public function getGamesFromArrayId(array $gameIds)
	{
		$query = $this->createQueryBuilder('g')
    		->select('g, po, two, d')
	    	->join('g.team_one', 'po')
	    	->join('g.team_two', 'two')
	    	->leftJoin('g.duels', 'd')
	    	->where('g.finished = false and g.deleted=false and g.active = true and g.id in (:ids)')
	    	->setParameter('ids', $gameIds);
		$query = $query->getQuery();
    	$r = $query->getResult();
		return $r;
	}

	public function getGamesDontStart()
	{
		$query = $this->createQueryBuilder('g')
    		->select('g, po, two')
	    	->join('g.team_one', 'po')
	    	->join('g.team_two', 'two')
	    	->where('g.finished = false and g.deleted=false and g.active = false');
		$query = $query->getQuery();
    	$r = $query->getResult();
		return $r;
	}
}