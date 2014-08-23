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
		
		$query =  $this->createQuery(
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
			
		
}