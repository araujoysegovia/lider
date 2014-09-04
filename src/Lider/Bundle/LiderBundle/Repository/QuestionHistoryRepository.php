<?php
namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class QuestionHistoryRepository extends MainMongoRepository
{
	public function getPlayerReports($player){
		$query = $dm->createQueryBuilder('LiderBundle:Session')
			->group(array('', '', ''), array('count' => 0))
		    ->reduce('function (obj, prev) { prev.count++; }')
		    ->field('a')->gt(1)
		    ->getQuery()
		    ->execute();
		return $query;
	}
}