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
    	$queryTotal =  $this->createQueryBuilder('d')
						->select('d')
						->join('d.player_one', 'po', 'WITH', 'po.deleted = false')
						->join('d.player_two', 'pt', 'WITH', 'pt.deleted = false')
						->leftJoin('po.team', 'to', 'WITH', 'to.deleted = false')
						->leftJoin('pt.team', 'tt', 'WITH', 'tt.deleted = false')
						->join('d.tournament', 't', 'WITH', 't.deleted = false AND t.id = :ti')
						->join('d.game', 'g', 'WITH', 'g.deleted = false AND g.level = :l')
						->where('d.deleted = false AND d.finished = TRUE AND (to.id = :tei or tt.id = :tei)')
						->setParameter('ti', $tournamentId)
						->setParameter('l', 1, \Doctrine\DBAL\Types\Type::INTEGER)
						->setParameter('tei', $teamId)
						->getQuery()
						->getArrayResult();

		$queryWin =  $this->createQueryBuilder('d')
						->select('d')
						->join('d.player_win', 'pw', 'WITH', 'pw.deleted = false')
						->leftJoin('pw.team', 'to', 'WITH', 'to.deleted = false')
						->join('d.tournament', 't', 'WITH', 't.deleted = false AND t.id = :ti')
						->join('d.game', 'g', 'WITH', 'g.deleted = false AND g.level = :l')
						->where('d.deleted = false AND d.finished = TRUE AND to.id = :tei')
						->setParameter('ti', $tournamentId)
						->setParameter('l', 1, \Doctrine\DBAL\Types\Type::INTEGER)
						->setParameter('tei', $teamId)
						->getQuery()
						->getArrayResult();
		

						// ->getArrayResult();

		// print_r($queryTotal);
		// print_r($queryWin);

    	// $queryTotal= $em->createQuery('SELECT d FROM LiderBundle:Duel d 
    	// 								INNER JOIN LiderBundle:Player p ON (d.player_one = p.id OR d.player_two = p.id) AND p.deleted = FALSE AND p.team = :teamid
    	// 								WHERE d.tournament = :tournamentid ')
			  //          ->setParameter('teamid', $teamId)
			  //          ->setParameter('tournamentid', $tournamentId);
			  //          echo $queryTotal->getSQL();
					//    // ->getArrayResult();

		// $queryWin= $em->createQuery('SELECT COUNT(d) as w FROM LiderBundle:Duel d 
  //   									INNER JOIN LiderBundle:Player p ON d.player_win = p.id AND p.deleted = FALSE
  //   									INNER JOIN LiderBundle:Team t ON p.team = t.id AND t.id = :teamid AND t.deleted = FALSE
  //   									INNER JOIN LiderBundle:Tournament to ON d.tournament = to.id AND to.id = :tournamentid AND to.deleted = FALSE')
		// 	           ->setParameter('teamid', $teamId)
		// 	           ->setParameter('tournamentid', $tournamentId)
		// 			   ->getArrayResult();

    	$result = array(
    		"win" => count($queryWin),
    		"total" => count($queryTotal),
    	);
    	return $result;
    }

    public function getDuelsByGame($gameId)
    {
    	$query =  $this->createQueryBuilder('d')
						->select('d, po, pt, to, tt, g')
						->join('d.player_one', 'po', 'WITH', 'po.deleted = FALSE')
						->join('d.player_two', 'pt', 'WITH', 'pt.deleted = FALSE')
						->join('po.team', 'to', 'WITH', 'to.deleted = FALSE')
						->join('pt.team', 'tt', 'WITH', 'tt.deleted = FALSE')
						->join('d.game', 'g', 'WITH', 'g.deleted = FALSE AND g.id = :ga')
						->where('d.deleted = false')
						->setParameter('ga', $gameId);

		$query = $query->getQuery();
		$r = $query->getResult();
		
		return $r;
    }

    public function getDuelsByGameArray($gameId)
    {
    	$query =  $this->createQueryBuilder('d')
						->select('d, po, pt, to, tt, g')
						->join('d.player_one', 'po', 'WITH', 'po.deleted = FALSE')
						->join('d.player_two', 'pt', 'WITH', 'pt.deleted = FALSE')
						->join('po.team', 'to', 'WITH', 'to.deleted = FALSE')
						->join('pt.team', 'tt', 'WITH', 'tt.deleted = FALSE')
						->join('d.game', 'g', 'WITH', 'g.deleted = FALSE AND g.id = :ga')
						->where('d.deleted = false')
						->setParameter('ga', $gameId);

		$query = $query->getQuery();
		$r = $query->getArrayResult();
		
		return $r;
    }
}