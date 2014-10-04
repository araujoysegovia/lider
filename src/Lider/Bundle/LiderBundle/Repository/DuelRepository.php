<?php
namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class DuelRepository extends MainRepository
{
	
	public function findCurrentPlayerDuel($player) {
		$query = $this->createQueryBuilder('d')
			->select('d, p1, p2')
			->join("d.player_one", "p1")
			->join("d.player_two", "p2")
			->where('d.active = true AND (d.player_one = :player OR d.player_two = :player)')
			->setParameter('player', $player)
			->getQuery();
		
		return $query->getArrayResult();
	}
	
	public function findHistoryPlayerDuel($player) {
		$query = $this->createQueryBuilder('d')
		->select('d, p1, p2')
		->join("d.player_one", "p1")
		->join("d.player_two", "p2")
		->where('d.active = false AND (d.player_one = :player OR d.player_two = :player)')
		->setParameter('player', $player)
		->getQuery();
	
		return $query->getArrayResult();
	}

	public function findDuelExpired($datetime){
		$datetime = $datetime." 00:00:00";
		$query = $this->createQueryBuilder('d')
		->select('d')
		->where('d,enddate >= :da AND d.active = FALSE')
		->setParameter('da', $datetime, \Doctrine\DBAL\Types\Type::DATETIME)
		->getQuery();

		return $query->getArrayResult();
	}

    public function getExpiredDuels($date)
    {
    	$query =  $this->createQueryBuilder('d')
						->select('d')
						->where('d.deleted = false AND d.enddate < :date AND d.active = true AND d.finished = false')
						->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATETIME);

		$query = $query->getQuery();
		$r = $query->getResult();
		
		return $r;
    }

    public function getTotalDuelWinnerByTeam($teamId, $tournamentId)
    {
    	$em = $this->getEntityManager();
    	$queryTotal= $em->createQuery('SELECT COUNT(d) as w FROM LiderBundle:Duel d 
    									INNER JOIN LiderBundle:Player p ON (d.player_one = p.id OR d.player_two = p.id) AND p.deleted = FALSE
    									INNER JOIN LiderBundle:Team t ON p.team = t.id AND t.id = :teamid AND t.deleted = FALSE
    									INNER JOIN LiderBundle:Tournament to ON d.tournament = to.id AND to.id = :tournamentid')
			           ->setParameter('teamid', $teamId)
			           ->setParameter('tournamentid', $tournamentId)
					   ->getArrayResult();

		$queryWin= $em->createQuery('SELECT COUNT(d) as w FROM LiderBundle:Duel d 
    									INNER JOIN LiderBundle:Player p ON d.player_win = p.id AND p.deleted = FALSE
    									INNER JOIN LiderBundle:Team t ON p.team = t.id AND t.id = :teamid AND t.deleted = FALSE
    									INNER JOIN LiderBundle:Tournament to ON d.tournament = to.id AND to.id = :tournamentid AND to.deleted = FALSE')
			           ->setParameter('teamid', $teamId)
			           ->setParameter('tournamentid', $tournamentId)
					   ->getArrayResult();

    	$result = array(
    		"win" => $queryWin[0]['w'],
    		"total" => $queryTotal[0]['w'],
    	);
    	return $result;
    }
}