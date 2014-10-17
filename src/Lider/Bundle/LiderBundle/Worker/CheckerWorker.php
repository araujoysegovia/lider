<?php
namespace Lider\Bundle\LiderBundle\Worker;

use Mmoreram\GearmanBundle\Driver\Gearman;
use Lider\Bundle\LiderBundle\Entity\Duel;

/**
 * @Gearman\Work(
 *     name = "chequear",
 *     description = "Worker for check games",
 *     defaultMethod = "doBackground",
 *     service="checkerWorker"
 * )
 */
class CheckerWorker
{

	private $co;

    public function __construct($co){
        $this->co = $co;        
    }

    /**     
     * @Gearman\Job(
     *     name = "checkDuel",
     *     description = "Check duel"
     * )
     */
	public function checkDuel(\GearmanJob $job)
    {
		$data = json_decode($job->workload(),true);
		$duelId = $data['duelId'];
		$userId = $data['userId'];

		$em = $this->co->get('doctrine')->getManager();

		$duel = $em->getRepository('LiderBundle:Duel')->find($duelId);
		$user = $em->getRepository('LiderBundle:Player')->find($userId);

		if(!$duel){
			return ;
		}
		
		

		$dm = $this->co->get('doctrine_mongodb')->getManager();
		


		$qhf = $this->co->get('question_manager')
						->getMissingQuestionFromDuel($duel, $duel->getPlayerOne());
		
		$qhs = $this->co->get('question_manager')
						->getMissingQuestionFromDuel($duel, $duel->getPlayerTwo());

		if(count($qhf) == 0 && count($qhs) == 0){
			$this->co->get('game_manager')->stopDuel($duel);
            $gameId = $duel->getGame()->getId();
			$this->checkGame($gameId);
		}

    }

    private function checkWinTeam($game)
    {
    	$em = $this->co->get('doctrine')->getManager();
    	$repo = $em->getRepository('LiderBundle:Duel');
    	$list = $repo->getDuelsByGameArray($game->getId());
    	$countT1 = 0;
        $countT2 = 0;
        $team1 = $list[0]['player_one']['team']['id'];
        $team2 = $list[0]['player_two']['team']['id'];
        foreach($list as $duel)
        {
            // echo "puntos del equipo ".$duel[->getPlayerOne()]->getTeam()->getName()." ".$duel->getPointOne()."\n";
            // echo "puntos del equipo ".$duel->getPlayerTwo()->getTeam()->getName()." ".$duel->getPointTwo()."\n";
            if($duel['point_one'] > $duel['point_two'])
            {
                $countT1++;
            }
            elseif($duel['point_one'] < $duel['point_two']){
                $countT2++;
            }
        }
        echo "puntos del equipo ".$list[0]['player_one']['team']['name']." = ".$countT1. "\n";
        echo "puntos del equipo ".$list[0]['player_two']['team']['name']." = ".$countT2. "\n";
    	if($countT1 < $countT2)
    	{
            $team = $em->getRepository("LiderBundle:Team")->find($team2);
            echo "gano el equipo 2 ".$team->getName()."\n";
    		return $team;
    	}
    	elseif($countT1 > $countT2)
    	{
            $team = $em->getRepository("LiderBundle:Team")->find($team1);
            echo "gano el equipo 1 ".$team->getName()."\n";
    		return $team;
    	}
    	return null;
    }

    /**
     * @Gearman\Job(
     *     name = "stopGameManual",
     *     description = "Detener el juego manualmente"
     * )
     */
    public  function stopGameManual(\GearmanJob $job){
    	$data = json_decode($job->workload(),true);
    	$gameId = $data['gameId'];
    	
    	$this->checkGame($gameId);
    }
    
	private function checkGame(&$gameId)
    {				
		//$duels = $game->getDuels()->findBy(array("active" => false, "finished" => true));
		$em = $this->co->get('doctrine')->getManager();
        $game = $em->getRepository("LiderBundle:Game")->find($gameId);
		$parameters = $this->co->get('parameters_manager')->getParameters();
		$duels = $em->getRepository('LiderBundle:Duel')->findBy(array("active" => true, "finished" => false, "game" =>$game));
		if(count($duels) == 0){
			$win = $this->checkWinTeam($game);
			if(!is_null($win))
			{
				$team = $em->getRepository('LiderBundle:Team')->find($win);
                $game->setTeamWinner($team);
				$team->setPoints($team->getPoints() + $parameters['gamesParameters']['gamePoints']);
                $gameManager = $this->co->get('game_manager');
                $gameManager->stopGame($game);
                $this->notificationPlayersGameFinish($game->getTeamOne(), $game->getTeamTwo(), $team);
                $this->notificationPlayersGameFinish($game->getTeamTwo(), $game->getTeamOne(), $team);
                $this->notificationToAdminGameFinish($game);
                $list = $em->getRepository("LiderBundle:Game")->findBy(array('active' => false, 'finished' => false,"tournament" => $game->getTournament()));
                if(count($list) == 0)
                {
                    echo "no existen juegos activos\n";
                    if($game->getTournament()->getLevel() < 5)
                    {
                        $tournament = $game->getTournament();
                        $tournament->setEnabledLevel(false);
                        $gearman = $this->co->get('gearman');
                        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotification', json_encode(array(
                            'subject' => 'Finalizacion de Nivel',
                            'templateData' => array(
                                'title' => 'Nivel finalizado',
                                'subjectUser' => 'Nivel finalizado',
                                'body' => '<p>El nivel '.$tournament->getLevel().' del torneo '.$tournament->getName().' ha finalizado. Por favor inicie el siguiente nivel</p>'
                            )
                        )));
                    }
                }
                $em->flush();
			}
			else
			{
				$team1 = $game->getTeamOne();
				$team2 = $game->getTeamTwo();
				$this->generateExtraDuel($team1, $team2, $game);
			}
		}
		
    }
    
    /**
     * @Gearman\Job(
     *     name = "sendNotificationPlayersGameFinish",
     *     description = "Envia notificaciones a los jugadores de los equipos cuando se finaliza el juego de manera manual"
     * )
     */
    public function sendNotificationPlayersGameFinish(\GearmanJob $job)
    {
    	$data = json_decode($job->workload(),true);
    	$gameId = $data['gameId'];
    	$em = $this->co->get('doctrine')->getManager();
    	$game = $em->getRepository("LiderBundle:Game")->findOneBy(array("id" => $gameId, "finished" => true, "deleted" => false, "active" => false));
    	if($game)
    	{
    		$win = $game->getTeamWinner();
    		if($win)
    		{
    			$this->notificationPlayersGameFinish($game->getTeamOne(), $game->getTeamTwo(), $win);
    			$this->notificationPlayersGameFinish($game->getTeamTwo(), $game->getTeamOne(), $win);
                $this->notificationToAdminGameFinish($game);
    		}
    	}
    }

    private function notificationToAdminGameFinish($game)
    {
        $gearman = $this->co->get('gearman');
        $to = array();
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotification', json_encode(array(
            'subject' => 'Juego Finalizado',
            'templateData' => array(
                'title' => 'Juego Finalizado',
                'subjectUser' => 'Juego finalizado entre '. $game->getTeamOne()->getName().' y '.$game->getTeamTwo()->getName()),
                'body' => 'El juego entre '. $game->getTeamOne()->getName().' y '.$game->getTeamTwo()->getName().' ha finalizado, y el ganador es '.$game->getTeamWinner()->getName()
        )));
    }
    
    private function notificationPlayersGameFinish($team, $vs, $win)
    {
        $gearman = $this->co->get('gearman');
        $to = array();
        $result = '';
        foreach($team->getPlayers() as $player)
        {
            $to[] = $player->getEmail();
        }
        if($team->getName() == $win->getName())
        {
            $result .= 'Tu Equipo ha ganado el juego contra ';
        }
        else{
            $result .= 'Tu Equipo ha perdido el juego contra ';
        }
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', json_encode(array(
            'subject' => 'Juego Finalizado',
            'to' => $to,
            'viewName' => 'LiderBundle:Templates:emailnotification.html.twig',
            'content' => array(
                'title' => 'Juego Finalizado',
                'subjectMessage' => 'Juego finalizado contra '.$vs->getName(),
                'body' => $result.$vs->getName()
            )
        )));
    }

    private function selectPlayer($team, $game)
    {
    	$dm = $this->co->get('doctrine_mongodb')->getManager();
    	$list = $dm->getRepository('LiderBundle:QuestionHistory')->findPlayersPlay($team->getId(), $game->getId());
    	$player = null;
    	if(count($team->getPlayers()) == count($list))
    	{
            echo "Entre cuando todos los jugadores jugaron\n";
            $listExtra = $dm->getRepository('LiderBundle:QuestionHistory')->findPlayersPlayInExtraDuel($team->getId(), $game->getId());
            if(count($team->getPlayers()) > count($listExtra))
            {
                foreach($team->getPlayers() as $play)
                {
                    $found = false;
                    foreach($listExtra as $pla)
                    {
                        if($play->getId() == $pla['player.playerId'])
                        {
                            $found = true;
                            break;
                        }

                    }
                    if(!$found)
                    {
                        echo "El jugador seleccionado es ".$play->getName()."\n";
                        $player = $play;
                        break;
                    }
                }
            }
            else{
                $totalPlayer = array();
                foreach($listExtra as $l)
                {
                    $totalPlayer[$l['player.playerId']] = $l['duels']; 
                }
                arsort($totalPlayers);
                $playerId = $totalPlayers[0];
                foreach($team->getPlayers() as $play)
                {
                    if($play->getId() == $pla['player.playerId'])
                    {
                           $player=$play;
                           break;
                    }
                }


                // $random = rand(0, count($list)-1);
                // echo "Cantidad de lista ".count($list)." numero aleatorio $random\n";
                // $players = $team->getPlayers();
                // $player = $players[$random];
            }
    		
    	}
    	else{
            echo "Entre cuando no jugaron todos\n";
    		foreach($team->getPlayers() as $play)
    		{
    			$found = false;
    			foreach($list as $pla)
    			{
    				if($play->getId() == $pla['player.playerId'])
    				{
    					$found = true;
    					break;
    				}

    			}
    			if(!$found)
    			{
                    echo "El jugador seleccionado es ".$play->getName()."\n";
    				$player = $play;
    				break;
    			}
    		}
    	}
    	return $player;
    }

    private function generateExtraDuel($team1, $team2, $game)
    {
        echo "se va a generar un extra duelo entre ". $team1->getName()." y el equipo ".$team2->getName();
        $gameManager = $this->co->get('game_manager');
        $em = $this->co->get('doctrine')->getManager();
        $pm = $this->co->get('parameters_manager');
        $params = $pm->getParameters();
    	$player1 = $this->selectPlayer($team1, $game);
    	$player2 = $this->selectPlayer($team2, $game);
        $date = new \DateTime();
        $endDate = new \DateTime();
        $endDate->modify('+'.$params['gamesParameters']['timeDuelExtra'].' day');
    	$duel = new Duel();
    	$duel->setGame($game);
    	$duel->setStartdate($date);
        $duel->setEndDate($endDate);
        $duel->setPlayerOne($player1);
        $duel->setPlayerTwo($player2);
        $duel->setTournament($game->getTournament());
        $duel->setExtraDuel(true);
        $countQuestion = $params['gamesParameters']['countQuestionDuelExtra'];
        $gameManager->generateQuestions($countQuestion, $duel);
        $em->persist($duel);
        $em->flush();
        $this->notificationExtraDuel($player1, $team2, $player2);
        $this->notificationExtraDuel($player2, $team1, $player1);
    }

    private function notificationExtraDuel($player, $teamvs, $playervs)
    {
        $gearman = $this->co->get('gearman');
        $body = 'Se ha generado un duelo definitivo entre tu equipo y el equipo '.$teamvs->getName().', y tu has sido el seleccionado para jugarlo contra '.$playervs->getName().' '.$playervs->getLastname();
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', json_encode(array(
            'subject' => 'Extra Duelo',
            'to' => $player->getEmail(),
            'viewName' => 'LiderBundle:Templates:emailnotification.html.twig',
            'content' => array(
                'title' => 'Tienes un Duelo Extra',
                'subjectMessage' => 'Se ha generado el duelo de desempate',
                'body' => $body
            )
        )));
    }
}