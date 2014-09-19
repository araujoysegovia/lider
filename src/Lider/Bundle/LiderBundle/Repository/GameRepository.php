<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class GameRepository extends MainRepository
{
	public function findGamesByGroup(){
		
		$query = $this->createQueryBuilder('g')
						->select('g, ga, to, tt')
						->join('g.games', 'ga', 'WITH','ga.deleted = false')
						->join('ga.team_one', 'to', 'WITH','to.deleted = false')
						->join('ga.team_two', 'tt', 'WITH','tt.deleted = false')
						->where('g.deleted = false')
						->orderBy('g.name', 'ASC');
		
		$query = $query->getQuery();
		//echo $query->getSQL()."<br/><br/>";		
		return $query->getArrayResult();
	}

}