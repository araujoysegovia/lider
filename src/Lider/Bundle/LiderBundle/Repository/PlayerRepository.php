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
		$query =  $em->createQuery(
		    'SELECT win.w, lost.l, point.p
		     FROM (
				SELECT COUNT(0) as w FROM LiderBundle:Duel WHERE player_win = :playerid
			 ) as win,
			 (
				SELECT COUNT(0) as w FROM LiderBundle:Duel WHERE player_lost = :playerid
			 ) as lost,
			 (
				SELECT SUM() as p FROM (
					
				) 
			 ) as lost,
		')->setParameter('playerid', $playerId);	
		
		return $query->getResult();
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
		
}