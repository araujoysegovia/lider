<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Lider\Bundle\LiderBundle\Lib\Normalizer;
use Lider\Bundle\LiderBundle\Entity\Game;
use Lider\Bundle\LiderBundle\Entity\Duel;

class GameManager
{
	private $em;
	private $dm;
	private $co;
	public static $COUNT_GAMES = 3;

	public function __construct($em, $dm){
		$this->em = $em;
		$this->dm = $dm;		
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

		//$tournament = $this->em->getRepository("LiderBundle:Tournament")->findOneBy(array("id" =>$tournamentId, "deleted" => false));
		$tournament = $this->em->getRepository("LiderBundle:Tournament")->getTournament($tournamentId);

		$games = $tournament->getGames();
		$duels = $tournament->getDuels();

		//Borrar equipos del torneo
		if(!is_null($games)){
			foreach ($games as $key => $value) {			
			$value->setDeleted(true);
			}
		}
		

		//Borrar duelos del torneo
		if(!is_null($duels)){
			foreach ($duels as $key => $value) {
				$value->setDeleted(true);
			}
		}

		if(!$tournament){
			throw new \Exception("Entity no found", 1);			
		}

		//print_r($tournament->getTeams());
		//echo get_class($tournament);

		if($tournament->getLevel() == 1){
			$this->generateGameForFirtsLevel($tournament, $interval);
		}else if($tournament->getLevel() == 2){
			$this->generateGameForSecondLevel();
		}
	}

	/**
	 * Generar juegos para el primer nivel
	 */
	private function generateGameForFirtsLevel($tournament, $interval)
	{
		$groups = $tournament->getGroups()->toArray();		
		
		

		foreach ($groups as $key => $value) {
			$lastDate = new \DateTime($tournament->getStartdate()->format('Y-m-d H:i:s'));			
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
					$lastDate->modify('+'.$interval.' day');
					echo "\n".$lastDate->format('Y-m-d H:i:s');
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
						$game->setGroup($value);
						$game->setTeamOne($teams[$p]);
						$game->setTeamTwo($teams[$v]);
						$game->setActive(false);
						$game->setStartDate(new \DateTime($lastDate->format('Y-m-d H:i:s')));
						$game->setFinished(false);
						$game->setLevel($tournament->getLevel());
						$game->setRound($i);
						$game->setTournament($tournament);
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

	private function generateGameForSecondLevel()
	{
		# code...
	}

	public function generateDuel($game, $interval)
	{
		$tournament = $this->em->getRepository("LiderBundle:Tournament")->getTournament($tournamentId);

		$games = $tournament->getGames();
		
		foreach ($games as $key => $game) {
			
			$lastDate = new \DateTime($tournament->getStartdate()->format('Y-m-d H:i:s'));

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

			for ($i=0; $i < $x ; $i++) { 
				
				$rand = rand(0, (count($secondPlayers) -1));	
				
				$playerOne = $firtsPlayers[$i];
				$playerTwo = $secondPlayers[$rand];

				array_splice($secondPlayers->toArray(), $rand, 1);

				$lastDate->modify('+'.$interval.' day');

				$duel = new Duel();
				$duel->setGame($game);
				$duel->setPlayerOne($playerOne);
				$duel->setPlayerTwo($playerTwo);
				$duel->setActive(false);
				$duel->setTournament($tournament);
				$duel->setStartDate(new \DateTime($lastDate->format('Y-m-d H:i:s')));

				$this->em->persist($duel);
			}
			
		}

		$this->em->flush();
	}
}
?>