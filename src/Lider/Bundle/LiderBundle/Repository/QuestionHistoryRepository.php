<?php
namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class QuestionHistoryRepository extends MainMongoRepository
{
	public function getPlayerReports($player){
		$query = $dm->createQueryBuilder('LiderBundle:Session')
			->group(array('tournament.tournamentId' => 1, 'question.categoryName' => 1, 'duel' => 1, 'find'=> 2), 
					array('count' => 0,'win' => 0,'lost' => 0))
		    ->reduce('function (obj, prev) { 
		    		prev.count++; 
		    		if(obj.find) prev.win++;
		    		else prev.lost++;
			}')
		    ->field('finished')->equals(true)
		    ->field('player.playerId')->equals($player->getId())
		    ->getQuery()
		    ->execute();
		
		return $query;
	}
}