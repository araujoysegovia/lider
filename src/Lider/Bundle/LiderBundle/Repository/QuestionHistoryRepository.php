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
		->group(array('player.playerId' => 1),
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

	public function findRangePosition(){
		$query = $this->createQueryBuilder("LiderBundle:QuestionHistory")
			->group(array("player.playerId" => 1, "player.name" => 2, 'player.lastname' => 2),
					array('win' => 0, 'lost' => 0, 'total' => 0, "totalPoint" => 0, 'fullname' => ''))
			->reduce('function (obj, prev){
					prev.fullname = obj.player.name + " " + obj.player.lastname;
					if(obj.duel){
						prev.count++;
						prev.totalPoint += obj.points;
			    		if(obj.find){
			    			prev.win++;
						}else{
			    			prev.lost++;
			    		}
					}
			}')
			->field('finished')->equals(true)
			// ->field('duel')->equals(true)
			->sort('points', 'asc')
			->getQuery()
			->execute();
	
		return $query;
	}
}