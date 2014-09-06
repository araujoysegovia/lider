<?php
namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class QuestionHistoryRepository extends MainMongoRepository
{
	public function getPlayerReports($player){
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->group(array('question.categoryName' => 1, 'duel' => 1), 
					array('count' => 0, 'countTest' => 0, 'winTest' => 0,'lostTest' => 0, 'win' => 0,'lost' => 0))
		    ->reduce('function (obj, prev) { 
		    		
		    		if(obj.duel){
		    			prev.count++; 
		    			if(obj.find) prev.win++;
			    		else prev.lost++;
					}else{
		    			prev.countTest++; 
			    		if(obj.find) prev.winTest++;
			    		else prev.lostTest++;
		    		}
			}')
		    ->field('finished')->equals(true)
		    ->field('player.playerId')->equals($player->getId())
		    //->field('tournament.tournamentId')->notIn(array())
		    ->getQuery()
		    ->execute();
		
		return $query;
	}

	public function getPlayerTotalReports($player){
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
		->group(array('find' => 1),
				array('count' => 0, 'win' => 0,'lost' => 0))
				->reduce('function (obj, prev) {
					prev.count++;
		    		if(obj.find){
		    			prev.win++;
					}else{
		    			prev.lost++;
		    		}
				}')
				->field('finished')->equals(true)
				->field('player.playerId')->equals($player->getId())
				//->field('tournament.tournamentId')->notIn(array())
		->getQuery()
		->execute();
	
		return $query;
	}
}