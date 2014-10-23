<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class PlayerRepository extends MainRepository
{
	
	/**	
	 * Partidos ganados y partidos perdidos del jugador
	 * @param unknown $id
	 */
	public function getPlayerGamesInfo($playerId) {		
		$em = $this->getEntityManager();
		
		$queryWin = $em->createQuery('SELECT COUNT(d) as w FROM LiderBundle:Duel d WHERE d.player_win = :playerid')	
			           ->setParameter('playerid', $playerId)		
					   ->getArrayResult();
		
		$queryLost = $em->createQuery('SELECT COUNT(d) as l FROM LiderBundle:Duel d WHERE d.player_lost = :playerid')
						->setParameter('playerid', $playerId)
						->getArrayResult();
		
		$queryPointOne = $em->createQuery('SELECT SUM(d.point_one) AS pt1 FROM LiderBundle:Duel d WHERE d.player_one = :playerid')
							->setParameter('playerid', $playerId)
							->getArrayResult();
		
		$queryPointTwo = $em->createQuery('SELECT SUM(d.point_two) AS pt2 FROM LiderBundle:Duel d WHERE d.player_two = :playerid')
							->setParameter('playerid', $playerId)
							->getArrayResult();
		
		
		$points = $queryPointOne[0]['pt1'] + $queryPointTwo[0]['pt2'];
		
		$array = array(
			'win'=>$queryWin[0]['w'],
			'lost'=>$queryLost[0]['l'],
			'points' => $points
		);

		return $array;
	}

	
	public function playersForCity($city) {
		$query = $this->createQueryBuilder('p')
					->select('p')
					->join('p.office', 'o', 'WITH', "o.city =:cityName AND o.deleted = false" )
					->join('p.roles', 'r', 'WITH', "r.deleted = false AND r.name = 'USER'")
					->where('p.active = true AND p.deleted = false')
					->setParameter('cityName', $city, \Doctrine\DBAL\Types\Type::STRING)
					->getQuery();
		
		return $query->getArrayResult();
	}

	public function findAdmin(){
		$query = $this->createQueryBuilder('p')
		->select('p')
		->join('p.roles', 'r', 'WITH', "r.deleted = false AND r.name = 'ADMIN'")
		->where('p.active = true AND p.deleted = false')
		->getQuery();

		return $query->getResult();
	}

	public function findPlayerPoint($playerId, $points, $date)
	{
		$first = new \DateTime($date->format('Y-m-d H:i:s'));
		$second = new \DateTime($date->format('Y-m-d H:i:s'));
		$first->modify("-1 minute");
		$second->modify("+1 minute");
		$query = $this->createQueryBuilder('pp')
		->select('pp, p')
		->join('pp.player', 'p', 'WITH', 'p.id = :pi')
		->where('pp.points = :po AND pp.entrydate > :d and pp.entrydate < :dd')
		->setParameter('pi', $playerId)
		->setParameter('po', $points)
		->setParameter('d', $first, \Doctrine\DBAL\Types\Type::DATETIME)
		->setParameter('dd', $second, \Doctrine\DBAL\Types\Type::DATETIME)
		->getQuery();

		return $query->getResult();
	}
		
}