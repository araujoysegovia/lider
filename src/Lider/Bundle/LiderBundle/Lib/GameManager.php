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

	public function __construct($em, $dm, $pm, $qm, $co){
		$this->em = $em;
		$this->dm = $dm;
		$this->pm = $pm;
		$this->qm = $qm;
		$this->co = $co;
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
	public function generateGame($tournamentId, $interval, $date = null){
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
			$this->generateGameForSecondLevel($tournament, $interval, $date);
		}
		elseif($tournament->getLevel() > 2)
		{
			$this->generateGamesAfterSecondLevel($tournament, $interval, $date);
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
			$teams = array();
			foreach ($value->getTeams()->toArray() as $t){
				if(!$t->getDeleted()){
					$teams[] = $t;
				}
			}
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
					// echo "\n".$endDate->format('Y-m-d H:i:s');
					for ($j=1; $j <= (floor($countTeams/2)) ; $j++) {
						if($pos >= $countTeams){
							// echo "\n pos = ".$pos;
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
						$game->setGroup($value);

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

	private function generateGameForSecondLevel($tournament, $interval, $startDate)
	{
		$gearman = $this->co->get('gearman');
		$teams = $this->em->getRepository("LiderBundle:Team")
									 ->findBy(array("tournament" => $tournament->getId(), "deleted" => false));

		$groups = $this->em->getRepository("LiderBundle:Group")
									 ->findBy(array("tournament" => $tournament->getId(), "deleted" => false));
		$tournamentId = $tournament->getId();
		$leters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
		$duelRepo = $this->em->getRepository('LiderBundle:Duel');
		$questionHistoryRepository = $this->dm->getRepository('LiderBundle:QuestionHistory');
		$countTeams = count($teams);
		$nextRound = 0;
		$totalGroups = count($groups);

		// Ciclo para obtener la cantidad de equipos en la segunda ronda
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
		$teamGroups = array();
		foreach($groups as $group)
		{
			$keys = $this->getOrderGroup($group);
			$teamGroups[$group->getName()] = $keys;
		}
		$columnA = array();
		$columnB = array();
		$third = array();
		// Esparcir los Equipos
		$totalTeams = 0;
		$keys = array_keys($teamGroups);
		for($i=0; $i < count($teamGroups); $i = $i+2)
		{
			$name1 = $teamGroups[$keys[$i]];
			$name2 = $teamGroups[$keys[$i+1]];

			$columnA[] = array(
				0 => $name1[0],
				1 => $name2[1]
			);
			$columnB[] = array(
				0 => $name1[1],
				1 => $name2[0]
			);
			$third[] = $name1[2];
			$third[] = $name2[2];
			$totalTeams += 4;
		}
		$endDate = new \DateTime($startDate->format('Y-m-d H:i:s'));
		$endDate->modify('+'.($interval - 1).' day');
		$count = 0;
		for($i=0; $i < count($columnA); $i++)
		{
			$team1 = $this->em->getRepository('LiderBundle:Team')->find($columnA[$i][0]);
			$team2 = $this->em->getRepository('LiderBundle:Team')->find($columnA[$i][1]);
			$game = new Game();
			$game->setTeamOne($team1);
			$game->setTeamTwo($team2);
			$game->setActive(false);
			$game->setStartDate(new \DateTime($startDate->format('Y-m-d H:i:s')));
			$game->setFinished(false);
			$game->setLevel($tournament->getLevel());
			$game->setTournament($tournament);
			$game->setEnddate(new \DateTime($endDate->format('Y-m-d').' 23:59:00'));
			$game->setIndicator($leters[$count]);
			$count++;
			$this->em->persist($game);
		}
		for($i=0; $i < count($columnB); $i++)
		{
			$team1 = $this->em->getRepository('LiderBundle:Team')->find($columnB[$i][0]);
			$team2 = $this->em->getRepository('LiderBundle:Team')->find($columnB[$i][1]);
			$game = new Game();
			$game->setTeamOne($team1);
			$game->setTeamTwo($team2);
			$game->setActive(false);
			$game->setStartDate(new \DateTime($startDate->format('Y-m-d H:i:s')));
			$game->setFinished(false);
			$game->setLevel($tournament->getLevel());
			$game->setTournament($tournament);
			$game->setEnddate(new \DateTime($endDate->format('Y-m-d').' 23:59:00'));
			$game->setIndicator($leters[$count]);
			$count++;
			$this->em->persist($game);
		}
		$this->em->flush();

        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotificationGamesDontStart', json_encode(array(
            'subject' => 'Juegos generados',
            'content' => array(
                'title' => 'Juegos Generados',
            )
        )));
		// Esparcir terceros mejores en caso de que sea necesario
		// $con = $nextRound - $totalTeams;
		// if($con > 0)
		// {
		// 	$sum = 
		// 	for($i=0; $i < count($third); $i = $i+4)
		// 	{
		// 		$columnA[] = array(
		// 			0 => $third[$i],
		// 			0 => $third[$i+1],
		// 		)
		// 		$columnB[] = array(
		// 			0 => $third[$i+],
		// 			0 => $third[$i+1],
		// 		)
		// 	}
		// }
	}

	public function generateGamesAfterSecondLevel($tournament, $interval, $startDate)
	{
		$gameRepo = $this->em->getRepository("LiderBundle:Game");
		$previousLevel = $tournament->getLevel() - 1;
		$games = $gameRepo->findBy(array("tournament" => $tournament->getId(), "level" => $previousLevel, "deleted" => false), array("indicator" => "ASC"));
		$teams = array();
		$leters = array();
		foreach($games as $game)
		{
			$teams[] = $game->getTeamWinner();
			$leters[] = $game->getIndicator();
		}
		$endDate = new \DateTime($startDate->format('Y-m-d H:i:s'));
		$endDate->modify('+'.($interval - 1).' day');
		for($i = 0; $i < count($teams); $i = $i + 2)
		{
			$game = new Game();
			$game->setTeamOne($teams[$i]);
			$game->setTeamTwo($teams[$i+1]);
			$game->setActive(false);
			$game->setStartDate(new \DateTime($startDate->format('Y-m-d H:i:s')));
			$game->setFinished(false);
			$game->setLevel($tournament->getLevel());
			$game->setTournament($tournament);
			$game->setEnddate(new \DateTime($endDate->format('Y-m-d').' 23:59:00'));
			$game->setIndicator($leters[$i].$leters[$i+1]);
			$this->em->persist($game);
		}
		$this->em->flush();
	}

	public function getOrderGroup($group)
	{
		$duelRepo = $this->em->getRepository('LiderBundle:Duel');
		$questionHistoryRepository = $this->dm->getRepository('LiderBundle:QuestionHistory');
		$teamGroups[$group->getName()] = array();
		$tournamentId = $group->getTournament()->getId();
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
			$ct = $teams[$keys[$i]];
			for($y = $i+1; $y < count($teams); $y++)
			{
				$st = $teams[$keys[$y]];
				// Segundo Criterio por porcentaje de dulos ganados del equipo
				if($ct <= $st){
					$duelWinner1 = $duelRepo->getTotalDuelWinnerByTeam($keys[$i], $tournamentId);
					$duelWinner2 = $duelRepo->getTotalDuelWinnerByTeam($keys[$y], $tournamentId);
					$percentDuelWin1 = $duelWinner1['total'] * $duelWinner1['win'] / 100;
					$percentDuelWin2 = $duelWinner2['total'] * $duelWinner2['win'] / 100;
					if($percentDuelWin1 < $percentDuelWin2)
					{
						$teams = $this->sort($teams, $i, $y);
						$keys = array_keys($teams);
						$i--;
						break;
					}
					elseif($percentDuelWin1 == $percentDuelWin2)
					{
						// Tercer criterio por porcentaje de preguntas correctas
						$questionWinner1 = $questionHistoryRepository->findpercentOfQuestionWinByTeam($keys[$i], $tournamentId);
						$questionWinner2 = $questionHistoryRepository->findpercentOfQuestionWinByTeam($keys[$y], $tournamentId);
						$questionWinner1 = $questionWinner1->toArray();
						$questionWinner2 = $questionWinner2->toArray();
						$questionWinner1 = $questionWinner1[0];
						$questionWinner2 = $questionWinner2[0];
						$percentQuestionWin1 = $questionWinner1['total'] * $questionWinner1['win'] / 100;
						$percentQuestionWin2 = $questionWinner2['total'] * $questionWinner2['win'] / 100;
						if($percentQuestionWin1 < $percentQuestionWin2)
						{
							$teams = $this->sort($teams, $i, $y);
							$keys = array_keys($teams);
							$i--;
							break;
						}
						elseif($percentQuestionWin1 == $percentQuestionWin2)
						{
							$game = $this->em->getRepository('LiderBundle:Game')->findGameFromTwoTeams($keys[$i], $keys[$y], $tournamentId);
							if($game && $game[0]["team_one"]["id"] == $keys[$i])
							{
								if($game[0]["point_one"] < $game[0]["point_two"])
								{
									$teams = $this->sort($teams, $i, $y);
								}
								elseif($game[0]["point_one"] > $game[0]["point_two"])
								{
									$teams = $this->sort($teams, $y, $i);
								}
								$keys = array_keys($teams);
								break;
							}
							elseif($game && $game[0]["team_one"]["id"] == $keys[$y])
							{
								if($game[0]["point_one"] < $game[0]["point_two"])
								{
									$teams = $this->sort($teams, $y, $i);
								}
								elseif($game[0]["point_one"] > $game[0]["point_two"])
								{
									$teams = $this->sort($teams, $i, $y);
								}
								$keys = array_keys($teams);
								break;
							}
						}
					}
				}
				else{
					break;
				}
				
			}
		}
		return $keys;
	}

	private function sort($teams, $i, $y)
	{
		$keys = array_keys($teams);
		$st2 = array();
		for($a = 0; $a < count($teams); $a++)
		{
			if($a == $i)
			{
				$st2[$keys[$y]] = $teams[$keys[$y]];
			}
			elseif($a == $y)
			{
				$st2[$keys[$i]] = $teams[$keys[$i]];
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

		$playersTeamOne = array();
		
		foreach ($teamOne->getPlayers() as $p){
			if(!$p->getDeleted()){
				if($p->getActive()){
					$playersTeamOne[] = $p;
				}
			}
		}
		
		$playersTeamTwo= array();
		foreach ($teamTwo->getPlayers() as $p){
			if(!$p->getDeleted()){
				if($p->getActive()){
					$playersTeamTwo[] = $p;
				}
			}
		}		
		
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
		$arraySecondPlayer = $secondPlayers;
		
		for ($i=0; $i < $x ; $i++) { 
			$startDate = new \DateTime($game->getStartdate()->format('Y-m-d H:i:s'));
			$endDate = new \DateTime($game->getStartdate()->format('Y-m-d H:i:s'));
			$rand = rand(0, (count($arraySecondPlayer) -1));	
			
			$playerOne = $firtsPlayers[$i];
			$playerTwo = $arraySecondPlayer[$rand];

			array_splice($arraySecondPlayer, $rand, 1);

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
			$this->notificationDuel($playerOne, $playerTwo->getTeam(), $playerTwo);
			$this->notificationDuel($playerTwo, $playerOne->getTeam(), $playerOne);
		}			

		$this->em->flush();
	}

	/**
	 * Notificar jugador de su duelo
	 */
	private function notificationDuel($player, $teamvs, $playervs)
    {
        $gearman = $this->co->get('gearman');
        $body = 'Hola <b>'.$player->getName().' '.$player->getLastname().': '.$player->getEmail().'</b><br><br> Se ha generado un duelo entre tu equipo y el equipo '.$teamvs->getName().', y tu has sido el seleccionado para jugarlo contra <b>'.$playervs->getName().' '.$playervs->getLastname().'</b>';
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', json_encode(array(
            'subject' => 'Duelo generado',
            'to' => $player->getEmail(),
            'viewName' => 'LiderBundle:Templates:emailnotification.html.twig',
            'content' => array(
                'title' => 'Tienes un Duelo',
                'subjectMessage' => 'Se ha generado tu duelo',
                'body' => $body
            )
        )));
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
		$gamesId = array();
		foreach ($games as $key => $game) {
			$game->setFinished(false);
			$game->setActive(true);
			$this->em->persist($game);
			$gamesId[] = $game->getId();
			$this->generateDuel($game, $duelInterval);
		}
		$this->em->flush();
		$gearman = $this->co->get('gearman');
		$result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotificationDuels', json_encode(array(
            'subject' => 'Duelos generado',
            'template' => 'LiderBundle:Templates:duelsnotificationadmin.html.twig',
            'content' => array(
                'title' => 'Duelos Generados',
                'games' => $gamesId
            )
        )));
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

	// private function stopGamesNoHaveActiveDuels($gameId)
	// {
	// 	$games = $this->em->getRepository("LiderBundle:duel")->getGamesNoHaveActiveDuels();
	// 	$gearman = $this->co->get('gearman');
	// 	foreach ($games as $key => $game) {
	// 		$result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkerchequear~stopGameManual', json_encode(array(
	//             'gameId' => $game['id'],
	//         )));
	// 	}

		
	// }

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
	public function stopGame($gameId)
	{
		$game = $this->em->getRepository("LiderBundle:Game")->find($gameId);
		// echo "entre al stopGame con el juego ".$game->getId()."\n";
		$game->setActive(false);
		// echo "puse el juego ".$game->getId()." en active false\n";
		$game->setFinished(true);
		// echo "puse el juego ".$game->getId()." en finished true\n";
		$this->em->flush();
		// echo "hice el flush\n";
		$duels = $game->getDuels();		

		foreach ($duels as $key => $duel) {
			$duel->setActive(false);
			$duel->setFinished(true);
		}

		$this->em->flush();
	}
}
?>