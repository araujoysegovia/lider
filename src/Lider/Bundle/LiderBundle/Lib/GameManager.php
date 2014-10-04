<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Lider\Bundle\LiderBundle\Lib\Normalizer;
use Lider\Bundle\LiderBundle\Entity\Game;
use Lider\Bundle\LiderBundle\Entity\Duel;
use Lider\Bundle\LiderBundle\Entity\DuelQuestion;

class GameManager
{
	private $em;
	private $dm;
	private $co;
	private $pm;
	private $qm;
	private $base2;
	public static $COUNT_GAMES = 3;

	public function __construct($em, $dm, $pm, $qm){
		$this->em = $em;
		$this->dm = $dm;		
		$this->pm = $pm;		
		$this->qm = $qm;
	}

	public function endDuels(){
		$repo = $this->em->getRepository("LiderBundle:Duel");
		$list = $repo->findDuelExpired(new \DateTime());
		foreach($list as $key => $value)
		{
			$value->setActive(false);
		}
		$this->em->flush();
	}

	/**
	 * Generar juegos
	 */
	public function generateGame($tournamentId, $interval){
		$teams = $this->em->getRepository("LiderBundle:Team")
						  ->findBy(array("tournament" => $tournamentId, "deleted" => false));

		if(count($teams) > 0)
		{
			$this->base2 = array();
			$i = 0;
			$x = 0;
			while($i <= count($teams))
			{
				$i = 2^$x;
				$this->base2[] = $i;
				$x++;
			}

		}
		//$tournament = $this->em->getRepository("LiderBundle:Tournament")->findOneBy(array("id" =>$tournamentId, "deleted" => false));
		$tournament = $this->em->getRepository("LiderBundle:Tournament")->getTournament($tournamentId);
		if(!$tournament){
			throw new \Exception("Entity no found", 1);			
		}
		$games = $this->em->getRepository("LiderBundle:Game")
						  ->findBy(array("tournament" => $tournament->getId(), "deleted" => false, "level" => $tournament->getLevel()));
		//$duels = $tournament->getDuels();

		//Borrar juegos del torneo
		if(!is_null($games)){
			foreach ($games as $key => $game) {			
				$game->setDeleted(true);
				//Borrar duelos del torneo
				if(!is_null($game->getDuels())){
					foreach ($game->getDuels() as $key => $duel) {
						$duel->setDeleted(true);
					}
				}
			}
		}

		if($tournament->getLevel() == 1){
			$this->generateGameForFirtsLevel($tournament, $interval);
		}elseif($tournament->getLevel() == 2){
			$this->generateGameForSecondLevel();
		}
	}

	/**
	 * Generar juegos para el primer nivel
	 */
	private function generateGameForFirtsLevel($tournament, $interval)
	{		
		$groups = $this->em->getRepository("LiderBundle:Group")
						   ->findBy(array("tournament" => $tournament->getId(), "deleted" => false));
	
		foreach ($groups as $key => $value) {
			$startDate = new \DateTime($tournament->getStartdate()->format('Y-m-d H:i:s'));			
			//echo "\n\n";
			//echo "\n ----------------------------- ".$value->getName()." ---------------------------------------";
			$teams = $value->getTeams()->toArray();
			// foreach ($teams as $key => $val) {
			// 	echo "\n········".$val->getName()."········";
			// }
			// echo "\n\n";
			$countTeams = count($teams);

			if($countTeams == 3 || ($countTeams%2 == 0)){

				for ($i=1; $i <= self::$COUNT_GAMES ; $i++) { 
					$pos = 0;
					$vs = 0;

					if($i !=1){
						$startDate->modify('+'.$interval.' day');
					}										

					$endDate = new \DateTime($startDate->format('Y-m-d H:i:s'));
					$endDate->modify('+'.($interval - 1).' day');
					//echo "\n".$lastDate->format('Y-m-d H:i:s');
					for ($j=1; $j <= (floor($countTeams/2)) ; $j++) { 
						if($pos >= $countTeams){
							//echo "\n pos = ".$pos;
							$x = floor($pos/$countTeams);
							//echo "\n x = ".$x;
							$pos = ($pos-1) - $x;
							//echo "\n pos = ".$pos;
						}

						$p = $pos;
						$v= $p +$i;

						if($v > $countTeams){
							$v = $v - $countTeams;
						}

						//echo "\t $p=".$teams[$p]->getName()." VS $v=".$teams[$v]->getName()."\n";
						//$now = new Date();
						
						$game = new Game();
						$game->setTeamOne($teams[$p]);
						$game->setTeamTwo($teams[$v]);
						$game->setActive(false);
						$game->setStartDate(new \DateTime($startDate->format('Y-m-d H:i:s')));
						$game->setFinished(false);
						$game->setLevel($tournament->getLevel());
						$game->setRound($i);
						$game->setTournament($tournament);
						$game->setEnddate(new \DateTime($endDate->format('Y-m-d').' 23:59:00'));

						$this->em->persist($game);
						
						$pos = $i +1;

					}
				}
			}else{
				throw new \Exception("El grupo ".$value->getName()." no tiene la cantidad valida de equipos", 1);
				
			}

			//echo "\n ----------------------------------------------------------------------";
		}
		$this->em->flush();
	}

	private function generateGameForSecondLevel($tournamentId)
	{
		$teams = $this->em->getRepository("LiderBundle:Team")
									 ->findBy(array("tournament" => $tournamentId, "deleted" => false));

		$groups = $this->em->getRepository("LiderBundle:Group")
									 ->findBy(array("tournament" => $tournament->getId(), "deleted" => false));
		$duelRepo = $this->em->getRepository('LiderBundle:Duel');
		$questionHistoryRepository = $this->em->getRepository('LiderBundle:QuestionHistory');
		$countTeams = count($teams);
		$nextRound = 0;
		$totalGroups = count($groups);
		for($i=0; $i < count($this->base2); $i++)
		{
			if($countTeams < $this->base2[$i])
			{
				$nextRound = $this->base2[$i-1];
				break;
			}
			elseif($countTeams == $this->base2[$i])
			{
				$nextRound = $this->base2[$i];
				break;
			}
		}
		$mod = $nextRound % $totalGroups;
		$tot = $nextRound / $totalGroups;
		$total = intval($tot);
		$arr1 = array();
		$arr2 = array();
		$thids = array();
		$teamGroups = array();
		foreach($groups as $group)
		{
			$teamGroups[$group->getName()] = array();
			$teams = array();
			foreach($group->getTeams() as $team)
			{
				$teams[$team->getId()] = $team->getPoints();
			}
			arsort($teams);
			$keys = array_keys($teams);
			for($i = 0;$i < count($teams); $i++)
			{
				$z = $i;
				$ct = $teams[$key[$i]];
				for($y = $i+1; $y <= count($teams); $y++)
				{
					$st = $teams[$key[$y]];
					if($ct <= $st){
						$result1 = $duelRepo->getTotalDuelWinnerByTeam($teams[$keys[$i]], $tournamentId);
						$result2 = $duelRepo->getTotalDuelWinnerByTeam($teams[$keys[$y]], $tournamentId);
						$percent1 = $result1['total'] * $result1['win'] / 100;
						$percent2 = $result2['total'] * $result2['win'] / 100;
						if($percent1 < $percent2)
						{
							$teams = $this->sort($percent1, $percent2, $teams, $i, $y);
							$keys = array_keys($teams);
							$i--;
						}
						elseif($percent1 == $percent2))
						{
							$result1 = $questionHistoryRepository->findpercentOfQuestionWinByTeam($teams[$keys[$i]], $tournamentId);
							$result2 = $questionHistoryRepository->findpercentOfQuestionWinByTeam($teams[$keys[$y]], $tournamentId);
							$percent1 = $result1['total'] * $result1['win'] / 100;
							$percent2 = $result2['total'] * $result2['win'] / 100;
							if($percent1 < $percent2)
							{
								$teams = $this->sort($percent1, $percent2, $teams, $i, $y);
								$keys = array_keys($teams);
								$i--;
							}
						}
					}
				}
			}
			$teamGroups[$group->getName()] = $keys;
		}
		// Esparcir los Equipos
	}

	private function sort($percent1, $percente2, $teams, $i, $y)
	{
		$keys = array_keys($teams);
		$st2 = array();
		for($a = 0; $a <= count($teams); $a++)
		{
			if($a == $i)
			{
				$st2[$keys[$a]] = $teams[$keys[$y]];
			}
			elseif($a == $y)
			{
				$st2[$keys[$a]] = $teams[$keys[$i]];
			}
			else
			{
				$st2[$keys[$a]] = $teams[$keys[$a]];
			}
		}
		return $st2;
	}

	/**
	 * Generar los duelos de un juego
	 */
	public function generateDuel($game, $interval)
	{		
		//$game = $this->em->getRepository("LiderBundle:Game")->findOneBy(array("id" => $gameId, "deleted" => false));

		$teamOne = $game->getTeamOne();
		$teamTwo = $game->getTeamTwo();

		$playersTeamOne = $teamOne->getPlayers();
		$playersTeamTwo= $teamTwo->getPlayers();

		$countPlayersTeamOne = count($playersTeamOne);
		$countPlayersTeamTwo = count($playersTeamTwo);

		$x = $countPlayersTeamOne;
		
		$firtsPlayers = $playersTeamOne;
		$secondPlayers = $playersTeamTwo;

		if($countPlayersTeamOne != $countPlayersTeamTwo){				
			if($countPlayersTeamOne > $countPlayersTeamTwo){					
				$x = $countPlayersTeamTwo;
				$firtsPlayers = $playersTeamTwo;
				$secondPlayers = $playersTeamOne;
			}
		}

		$params = $this->pm->getParameters();
		$countQuestion = $params['gamesParameters']['countQuestionDuel'];
		
		for ($i=0; $i < $x ; $i++) { 
			$startDate = new \DateTime($game->getStartdate()->format('Y-m-d H:i:s'));
			$endDate = new \DateTime($game->getStartdate()->format('Y-m-d H:i:s'));
			$rand = rand(0, (count($secondPlayers) -1));	
			
			$playerOne = $firtsPlayers[$i];
			$playerTwo = $secondPlayers[$rand];

			array_splice($secondPlayers->toArray(), $rand, 1);

			$endDate->modify('+'.($interval-1).' day');

			$duel = new Duel();
			$duel->setGame($game);
			$duel->setPlayerOne($playerOne);
			$duel->setPlayerTwo($playerTwo);
			$duel->setActive(true);
			$duel->setTournament($game->getTournament());
			$duel->setStartDate($startDate);
			$duel->setEnddate(new \DateTime($endDate->format('Y-m-d').' 23:59:00'));
			$this->em->persist($duel);

			$this->generateQuestions($countQuestion, $duel);
		}			

		$this->em->flush();
	}


	/**
	 * Detener los juegos hasta la fecha actual
	 */
	public function stopGames()
	{
		$date = new \DateTime();		
		$games = $this->em->getRepository("LiderBundle:Game")->getExpiredGame($date);
				
		foreach ($games as $key => $game) {
			
			$game->setFinished(true);
			$game->setActive(false);
			$this->em->persist($game);
			$duels = $game->getDuels();

			foreach ($duels as $key => $duel) {
				$duel->setFinished(true);
				$duel->setActive(false);
				$this->em->persist($duel);
			}
		}

		$this->em->flush();
	}

	/**
	 * Iniciar juegos hasta la fecha actual
	 */
	public function startGames()
	{
		$date = new \DateTime();		
		$games = $this->em->getRepository("LiderBundle:Game")->getGamesToStart($date);

		$params = $this->pm->getParameters();
		$duelInterval = $params['gamesParameters']['timeDuel'];
				
		foreach ($games as $key => $game) {
			
			$game->setFinished(false);
			$game->setActive(true);
			$this->em->persist($game);
			$this->generateDuel($game, $duelInterval);
		}

		$this->em->flush();
	}	

	/**
	 * Detener duelos hasta la fecha actual
	 */
	public function stopDuels()
	{
		$date = new \DateTime();		
		$duels = $this->em->getRepository("LiderBundle:Duel")->getExpiredDuels($date);
				
		foreach ($duels as $key => $duel) {
			
			$duel->setFinished(true);
			$duel->setActive(false);
					
		}

		$this->em->flush();
	}	

	/**
	 * Generar preguntas para un duelo
	 */
	public function generateQuestions($count, $duel)
	{
		$questions = $this->qm->generateEntityQuestions($count, $duel);

		foreach ($questions as $key => $question) {
			$duelQuestion = new DuelQuestion();

			$duelQuestion->setQuestion($question);
			$duelQuestion->setDuel($duel);
			$duelQuestion->setGame($duel->getGame());
			$this->em->persist($duelQuestion);
		}

		$this->em->flush();
	}

	/**
	 * Detener un duelo
	 */
	public function stopDuel($duel)
	{
		$duel->setActive(false);
		$duel->setFinished(true);

		$this->em->flush();
	}

	/**
	 * Detener un juego y finalizer sus duelos
	 */
	public function stopGame($game)
	{
		$game->setActive(false);
		$game->setFinished(true);

		$duels = $game->getDuels();		

		foreach ($duels as $key => $duel) {
			$duel->setActive(false);
			$duel->setFinished(true);
		}

		$this->em->flush();
	}
}
?>