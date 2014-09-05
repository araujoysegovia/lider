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
			
		
}