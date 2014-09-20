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
		echo $datetime."\n";
		$datetime = $datetime." 00:00:00";
		echo $datetime."\n";
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
			
		
}