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
		
		echo "Voy a comparar la cantidad de preugntas del jugador ".$duel->getPlayerOne()->getname()." ".$duel->getPlayerOne()->getLastname(). " y ".$duel->getPlayerTwo()->getname()." ".$duel->getPlayerTwo()->getLastname()."\n";
        echo "Cantidad de preguntas del jugador UNO ".$duel->getPlayerOne()->getname()." = ".count($qhf)."\n";
        echo "Cantidad de preguntas del jugador DOS ".$duel->getPlayerTwo()->getname()." = ".count($qhs)."\n";
		if(count($qhf) == 0 && count($qhs) == 0){
			echo "entre cuando no hay preguntas\n";
            echo "\nFinalizar duelo: ".$duel->getId();
			$this->co->get('game_manager')->stopDuel($duel);

            echo "\nDespues de setear ";
            echo "\nActive :".$duel->getActive();
            echo "\nFinished: ".$duel->getFinished();


            $point1 = $duel->getPointOne();
            $point2 = $duel->getPointTwo();
            echo "\nCantidad de puntos del jugador ".$duel->getPlayerOne()->getname()." = ".$point1." En el equipo ".$duel->getPlayerOne()->getTeam()->getName()."\n";
            echo "\nCantidad de puntos del jugador ".$duel->getPlayerTwo()->getname()." = ".$point2." En el equipo ".$duel->getPlayerTwo()->getTeam()->getName()."\n";
            if($point1 < $point2)
            {
                echo "Gano El jugador ".$duel->getPlayerTwo()->getName()." ".$duel->getPlayerTwo()->getLastname()."\n";
                $duel->setPlayerWin($duel->getPlayerTwo());
            }
            elseif($point1 > $point2)
            {
                echo "Gano El Duelo el jugador ".$duel->getPlayerOne()->getName()." ".$duel->getPlayerOne()->getLastname()."\n";
                $duel->setPlayerWin($duel->getPlayerOne());
            }
            $duel->setActive(false);
            $duel->setFinished(true);


            $em->flush();

            
            echo "\nDespues de setear 2";
            echo "\nActive :".$duel->getActive();
            echo "\nFinished: ".$duel->getFinished();
            // $gameId = $duel->getGame()->getId();
            $game = $duel->getGame();
			$this->checkGame($game);
		}

    }

    private function checkWinTeam($game)
    {
    	$em = $this->co->get('doctrine')->getManager();
    	$repo = $em->getRepository('LiderBundle:Duel');
    	$list = $repo->getDuelsByGameArray($game->getId());
    	$countT1 = 0;
        $countT2 = 0;
        $team1 = $em->getRepository("LiderBundle:Team")->find($list[0]['player_one']['team']['id']);
        $team2 = $em->getRepository("LiderBundle:Team")->find($list[0]['player_two']['team']['id']);
        foreach($list as $duel)
        {
//             echo "puntos del equipo ".$duel[->getPlayerOne()]->getTeam()->getName()." ".$duel->getPointOne()."\n";
//             echo "puntos del equipo ".$duel->getPlayerTwo()->getTeam()->getName()." ".$duel->getPointTwo()."\n";
//			print_r($duel['player_one']);
            if($duel['point_one'] > $duel['point_two'])
            {
            	foreach($team1->getPlayers() as $player)
            	{
            		if($duel['player_one']['id'] == $player->getId())
            		{
            			$countT1++;
            			break;
            		}
            	}
            	foreach($team2->getPlayers() as $player)
            	{
            		if($duel['player_one']['id'] == $player->getId())
            		{
            			$countT2++;
            			break;
            		}
            	}
            }
            elseif($duel['point_one'] < $duel['point_two']){
            foreach($team1->getPlayers() as $player)
            	{
            		if($duel['player_two']['id'] == $player->getId())
            		{
            			$countT1++;
            			break;
            		}
            	}
            	foreach($team2->getPlayers() as $player)
            	{
            		if($duel['player_two']['id'] == $player->getId())
            		{
            			$countT2++;
            			break;
            		}
            	}
            }
        }
        echo "puntos del equipo ".$list[0]['player_one']['team']['name']." = ".$countT1. "\n";
        echo "puntos del equipo ".$list[0]['player_two']['team']['name']." = ".$countT2. "\n";
    	if($countT1 < $countT2)
    	{
            echo "gano el equipo 2 ".$team2->getName()."\n";
    		return $team2;
    	}
    	elseif($countT1 > $countT2)
    	{
            echo "gano el equipo 1 ".$team1->getName()."\n";
    		return $team1;
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
        $em = $this->co->get('doctrine')->getManager();
    	$data = json_decode($job->workload(),true);
    	$gameId = $data['gameId'];
    	$game = $em->getRepository("LiderBundle:Game")->find($gameId);
    	$this->checkGame($game);
    }
    
	private function checkGame(&$game)
    {				
		//$duels = $game->getDuels()->findBy(array("active" => false, "finished" => true));
		$em = $this->co->get('doctrine')->getManager();
		
		$repoParameters = $em->getRepository("LiderBundle:Parameters");
		
        // $game = $em->getRepository("LiderBundle:Game")->find($gameId);
		///$parameters = $this->co->get('parameters_manager')->getParameters();
		$duels = $em->getRepository('LiderBundle:Duel')->findBy(array("finished" => false, "game" =>$game));
		echo count($duels);
		if(count($duels) == 0){
			$win = $this->checkWinTeam($game);
			if(!is_null($win))
			{
				$team = $em->getRepository('LiderBundle:Team')->find($win);
                $game->setTeamWinner($team);
                ///echo "SUme los puntos del equipo ".$team->getName()." tenia ".$team->getPoints()." y ahora tendra ".($team->getPoints()+$parameters['gamesParameters']['gamePoints'])."\n";
				///$team->setPoints($team->getPoints() + $parameters['gamesParameters']['gamePoints']);
                
                $gamePoints = $repoParameters->findOneBy(array('name'=>'gamePoints'));
				$gamePoints = $gamePoints->getValue(); 
				echo "\nSume los puntos del equipo ".$team->getName()." tenia ".$team->getPoints()." y ahora tendra ".($team->getPoints()+$gamePoints)."\n";
				$team->setPoints($team->getPoints() + $gamePoints);
				
                $gameManager = $this->co->get('game_manager');
                // echo "el juego ".$game->getId(). " se detendra\n";
                $gameManager->stopGame($game->getId());
                if($game->getTeamOne()->getId() == $win->getId())
                {
                    //$game->setPointOne($parameters['gamesParameters']['gamePoints']);
                	$game->setPointOne($gamePoints);
                }
                else{
                    //$game->setPointTwo($parameters['gamesParameters']['gamePoints']);
                	$game->setPointTwo($gamePoints);
                }
                // $game->setActive(false);
                // $game->setFinished(true);
                // $em->persist($game);
                $em->flush();
                $this->notificationPlayersGameFinish($game->getTeamOne(), $game->getTeamTwo(), $team);
                $this->notificationPlayersGameFinish($game->getTeamTwo(), $game->getTeamOne(), $team);
                $this->notificationToAdminGameFinish($game);
                $this->finishTournamentLevel($game->getId());
			}
			else
			{
				$team1 = $game->getTeamOne();
				$team2 = $game->getTeamTwo();
				$this->generateExtraDuel($team1, $team2, $game);
			}
		}
    }

    private function finishTournamentLevel($gameId)
    {
        $em = $this->co->get('doctrine')->getManager();
        $game = $em->getRepository("LiderBundle:Game")->getArrayEntityWithOneLevel(array("id" => $gameId));
        $list = $em->getRepository("LiderBundle:Game")->getArrayEntityWithOneLevel(array('finished' => false,"tournament" => $game['data'][0]['tournament']['id']));

        echo "cantidad de juegos no finalizados ".$list['total']."\n";
        if($list['total'] == 0)
        {
            $tournament = $em->getRepository("LiderBundle:Tournament")->find($game['data'][0]['tournament']['id']);
            echo "no existen juegos activos\n";
            if($tournament->getLevel() < 5)
            {
                echo "voy a activar el siguiente nivel\n";
                $tournament->setEnabledLevel(false);
                $level = $tournament->getLevel();
                $tournament->setLevel($tournament->getLevel()+1);
                $em->persist($tournament);
                $gearman = $this->co->get('gearman');
                
                $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotification', json_encode(array(
                    'subject' => 'Finalizacion de Nivel',
                    'templateData' => array(
                        'title' => 'Nivel finalizado',
                        'subjectUser' => 'Nivel finalizado',
                        'body' => '<p>El nivel '.$level.' del torneo '.$tournament->getName().' ha finalizado. Por favor inicie el siguiente nivel</p>'
                    )
                )));
            }
            else{
                $tournament->setActive(false);
            }
            $em->flush();
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
    	echo "\nEnviando notificaciones de juego finalizado\n";
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

    /**
     * @Gearman\Job(
     *     name = "sendNotificationPlayersDuel",
     *     description = "Envia notificaciones a los jugadores de los equipos cuando se finaliza el juego de manera manual"
     * )
     */
    public function sendNotificationPlayersDuel(\GearmanJob $job)
    {
        $data = json_decode($job->workload(),true);
        $gearman = $this->co->get('gearman');
        $duelId = $data['duelId'];
        $em = $this->co->get('doctrine')->getManager();
        $duel = $em->getRepository("LiderBundle:Duel")->findOneBy(array("id" => $duelId, "active" => true, "deleted" => false));
        if($duel)
        {
            if(!$duel->getExtraDuel())
            {
                $this->notificationDuel($duel->getPlayerOne(), $duel->getPlayerTwo()->getTeam(), $duel->getPlayerTwo());
                $this->notificationDuel($duel->getPlayerTwo(), $duel->getPlayerOne()->getTeam(), $duel->getPlayerOne());
            }
            else{
                $this->notificationExtraDuel($duel->getPlayerOne(), $duel->getPlayerTwo()->getTeam(), $duel->getPlayerTwo());
                $this->notificationExtraDuel($duel->getPlayerTwo(), $duel->getPlayerOne()->getTeam(), $duel->getPlayerOne());
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
                'subjectUser' => 'Juego finalizado entre '. $game->getTeamOne()->getName().' y '.$game->getTeamTwo()->getName(),
                'body' => 'El juego entre '. $game->getTeamOne()->getName().' y '.$game->getTeamTwo()->getName().' ha finalizado, y el ganador es '.$game->getTeamWinner()->getName()
            )
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
                if(count($listExtra) > 0)
                {
                    $totalPlayers = array();
                    foreach($listExtra as $l)
                    {
                        $totalPlayers[$l['player.playerId']] = $l['duels']; 
                    }
                    arsort($totalPlayers);
                    $keys = array_keys($totalPlayers);
                    $playerId = $keys[0];
                    foreach($team->getPlayers() as $play)
                    {
                        if($play->getId() == $playerId)
                        {
                               $player=$play;
                               break;
                        }
                    }
                }
                else{
                    $random = rand(0, count($team->getPlayers())-1);
                    $players = $team->getPlayers();
                    $player = $players[$random];
                }
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
        
        $repoParameters = $em->getRepository("LiderBundle:Parameters");
//         $pm = $this->co->get('parameters_manager');
//         $params = $pm->getParameters();
    	$player1 = $this->selectPlayer($team1, $game);
    	$player2 = $this->selectPlayer($team2, $game);
        $date = new \DateTime();
        $endDate = new \DateTime();
        $pTimeDuel = $repoParameters->findOneBy(array('name'=>'timeDuel'));
        $pTimeDuel = $pTimeDuel->getValue();
        //$endDate->modify('+'.$params['gamesParameters']['timeDuel'].' day');
        $endDate->modify('+'.$pTimeDuel.' day');
    	$duel = new Duel();
    	$duel->setGame($game);
    	$duel->setStartdate($date);
        $duel->setEndDate($endDate);
        $duel->setPlayerOne($player1);
        $duel->setPlayerTwo($player2);
        $duel->setTournament($game->getTournament());
        $duel->setExtraDuel(true);
        //$countQuestion = $params['gamesParameters']['countQuestionDuelExtra'];
        $countQuestion = $repoParameters->findOneBy(array('name'=>'countQuestionDuelExtra'));
        $countQuestion = $countQuestion->getValue();
        
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

    private function notificationDuel($player, $teamvs, $playervs)
    {
        $gearman = $this->co->get('gearman');
        $body = 'Se ha generado un duelo entre tu equipo y el equipo '.$teamvs->getName().', y tu has sido el seleccionado para jugarlo contra '.$playervs->getName().' '.$playervs->getLastname();
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', json_encode(array(
            'subject' => 'Duelo Generado',
            'to' => $player->getEmail(),
            'viewName' => 'LiderBundle:Templates:emailnotification.html.twig',
            'content' => array(
                'title' => 'Tienes un Duelo',
                'subjectMessage' => 'Se ha generado tu duelo',
                'body' => $body
            )
        )));
    }
}