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

	public function findTeamPointsByGame($gameId)
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->group(array('duelId' => 1),
				array('points' => 0))
			->reduce('function (obj, prev){
					prev.points += obj.points
			}')
			->field('gameId')->equals($gameId)
			->getQuery()
			->execute();

		return $query;
	}

	// public function findTeamPointsByGame($gameId)
	// {
	// 	$query = $this->createQueryBuilder('LiderBundle:Team')
	// 		->group(array('team.teamId' => 1),
	// 			array('points' => 0))
	// 		->reduce('function (obj, prev){
	// 				prev.points += obj.points
	// 		}')
	// 		->field('gameId')->equals($gameId)
	// 		->getQuery()
	// 		->execute();

	// 	return $query;
	// }

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

	public function findRangePosition($tournamentId){
		$query = $this->createQueryBuilder("LiderBundle:QuestionHistory")
			->group(array("player.playerId" => 1, "player.name" => 2, 'player.lastname' => 2),
					array('win' => 0, 'winHelp' => 0, 'lost' => 0, 'total' => 0, "totalPoint" => 0, 'fullname' => '', 'teamname' => ''))
			->reduce('function (obj, prev){
					prev.fullname = obj.player.name + " " + obj.player.lastname;
					prev.teamname = obj.team.name;
					if(obj.duel){
						prev.total++;
						prev.totalPoint += obj.points || 0;
			    		if(obj.find && !obj.useHelp){
			    			prev.win++;
						}
						else if(obj.find && obj.useHelp){
							prev.winHelp++;
						}else{
			    			prev.lost++;
			    		}
					}
			}')
			->field('finished')->equals(true)
			->field('tournament.tournamentId')->exists(true)
			->field('tournament.tournamentId')->equals($tournamentId)
			// ->field('duel')->equals(true)
			->getQuery()
			->execute();
	
		return $query;
	}

	public function findRangePositionByPractice($tournamentId){
		
		$query = $this->createQueryBuilder("LiderBundle:QuestionHistory")
			->group(array("player.playerId" => 1, "player.name" => 2, 'player.lastname' => 2),
					array('win' => 0, 'winHelp' => 0, 'lost' => 0, 'total' => 0, "totalPoint" => 0, 'fullname' => '', 'teamname' => ''))
			->reduce('function (obj, prev){
					prev.fullname = obj.player.name + " " + obj.player.lastname;					
					if(!obj.duel){
						prev.total++;
						prev.totalPoint += obj.points || 0;
			    		if(obj.find && !obj.useHelp){
			    			prev.win++;
						}
						else if(obj.find && obj.useHelp){
							prev.winHelp++;
						}else{
			    			prev.lost++;
			    		}
					}					
			}')
			->field('finished')->equals(true)
			->field('tournamentid')->equals('4')
			->getQuery()
			->execute();
	
		return $query;
	}	

	public function findGroupPosition($tournamentId){
		$query = $this->createQueryBuilder("LiderBundle:QuestionHistory")
			->group(array("group.groupId" => 1, "tournament.tournamentId" => 1, "group.name" => 2),
					array('win' => 0, 'lost' => 0, 'total' => 0, "totalPoint" => 0))
			->reduce('function (obj, prev){
					if(obj.duel){
						prev.total++;
						prev.totalPoint += obj.points || 0;
			    		if(obj.find){
			    			prev.win++;
						}else{
			    			prev.lost++;
			    		}
					}
			}')
			->field('finished')->equals(true)
			->field('group.groupId')->exists(true)
			->field('tournament.tournamentId')->exists(true)
			->field('tournament.tournamentId')->equals($tournamentId)
			// ->field('duel')->equals(true)
			//->sort('points', 'asc')
			->getQuery()
			->execute();
	
		return $query;
	}

	public function getMissingQuestionByDuel($duelId, array $question)
	{
		$query = $this->createQueryBuilder("LiderBundle:QuestionHistory")
					->field('duel')->equals(true)
					->field('duelId')->equals($duelId)
					->field('question.id')->notIn($question)
					->field('finished')->equals(true)
					->getQuery()
                    ->execute();
        return $query;
	}


	public function getQuestionFinishedForDuel($user, $duel)
	{
		$questionFinished = $this->createQueryBuilder('LiderBundle:QuestionHistory')
							->field('finished')->equals(true)
							->field('duel')->equals(true)
							->field('duelId')->equals($duel->getId())
							->field('player.playerId')->equals($user->getId())
							->getQuery()
							->execute();
		return $questionFinished;
	}

	public function findpercentOfQuestionWinByTeam($teamId, $tournamentId)
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->group(array("team.teamId" => 1),
					array('total' => 0, 'win' => 0, 'duelId' => 0))
			->reduce('function (obj, prev){
					prev.total++;
					prev.duelId = obj.teamId;
					if(obj.find){
						prev.win++;
					}
			}')
			->field('team.teamId')->equals($teamId)
			->field('tournament.tournamentId')->equals($tournamentId)
			->getQuery()
			->execute();
		return $query;
	}

	public function findperPointsInGame($teamId1, $teamId2, $tournamentId)
	{
		$query = $this->createQuertyBuilder('LiderBundle:QuestionHistory')
			->group(array("team.teamId" => 1),
					array('points' => 0))
			->reduce('function (obj, prev){
					prev.points = obj.points
			}')
			->field('team.teamId')->in(array($teamId1, $teamId2))
			->field('tournament.tournamentId')->equals($tournamentId)
			->getQuery()
			->execute();

		return $query;
	}

	public function findPlayersPlay($teamId, $gameId)
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
		->group(array('player.playerId' => 1),
				array('duels' => 0))
		->reduce('function(obj, prev){
				prev.duels ++;
		}')
		->field('gameId')->equals($gameId)
		->field('team.teamId')->equals($teamId)
		->getQuery()
		->execute();

		return $query;
	}

	public function findPlayersPlayInExtraDuel($teamId, $gameId)
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
		->group(array('player.playerId' => 1),
				array('duels' => 0))
		->reduce('function(obj, prev){
				prev.duels ++;
		}')
		->field('gameId')->equals($gameId)
		->field('team.teamId')->equals($teamId)
		->field('extraQuestion')->equals(true)
		->getQuery()
		->execute();

		return $query;
	}

	public function getQuestionForPlayer($tournamentId)
	{

		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->group(array("player.playerId" => 1),
					array('win' => 0, 'winHelp' => 0, 'lost' => 0, 'total' => 0, "totalPoint" => 0, 'playername' => '', 'teamname' => ''))				
			->reduce('function (obj, prev){
					prev.playername = obj.player.name + " " + obj.player.lastname;
					prev.total++;
					prev.totalPoint += obj.points || 0;
					prev.teamname = obj.team.name;
					if(obj.duel && obj.find){
						prev.win++;
					}
					else if(obj.find && obj.useHelp){
	 					prev.winHelp++;
		 			}else{
		 	    		prev.lost++;
		 	    	}
			}')
			->field('finished')->equals(true)
			->field('tournament.tournamentId')->equals($tournamentId)
			->getQuery()
			->execute();

		return $query;
	}

	public function getQuestionForPlayerByDuel($duelId)
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->group(array("player.playerId" => 1),
					array('total' => 0, 'win' =>0))
			->reduce('function (obj, prev){
					prev.total++;
					if(obj.duel && obj.find){
						prev.win++;
					}
			}')
			->field('finished')->equals(true)
			->field('duelId')->equals($duelId)
			->getQuery()
			->execute();
		return $query;
	}
	
	public function getPointsByDuel()
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
		->group(array('duelId' => 1, "player.playerId" => 1),
				array('total' => 0))
				->reduce('function (obj, prev){
					prev.total+= obj.points;
			}')
				->field('finished')->equals(true)
				->field('duel')->equals(true)
				->getQuery()
				->execute();
		return $query;
	}

	public function getQuestionReport()
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->group(array('question.questionId' => 1),
					array('total' => 0, 'win' => 0))
			->reduce('function (obj, prev){
					prev.total++;
					if(obj.find)
					{
						prev.win++;
					}
			}')
			->field('finished')->equals(true)
			->getQuery()
			->execute();
		return $query;
	}

	public function getGeneralCategoryReport($tournamentId = null)
	{
// 		echo $tournamentId;
		
// 		$stringTorneo = 
		
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
		  	->group(array("question.categoryName" => 1, "question.tournament" => 2),
					  array('win' => 0, 'lost' => 0, 'total' => 0))
			->reduce('function (obj, prev){
				prev.total++;
				if(obj.find){
					prev.win++;
				}else{
					prev.lost++;
				}
			}');
			//->field('tournament')->equals()
// 			->getQuery()
// 			->execute();
			
		if(!is_null($tournamentId))
		{
			$query->field("tournamentid")->equals($tournamentId);
		}
		
		$query = $query->getQuery()->execute();
		
		return $query;
	}

	public function getQuestionWithoutCategory()
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->field('question.categoryName')->exists(false)
			->getQuery()
			->execute();
		return $query;
	}


	public function questionsForPlayer()
	{
		$query = $this->createQueryBuilder('LiderBundle:QuestionHistory')
			->field('player.playerId')->equals(2)
			->getQuery()
			->execute();

		return $query;
	}
}