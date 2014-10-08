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
            echo "Juego Checkeado";
		}

    }

    private function checkWinTeam($game)
    {
    	$dm = $this->co->get('doctrine_mongodb')->getManager();
    	$repo = $dm->getRepository('LiderBundle:QuestionHistory');
    	$list = $repo->findTeamPointsByGame($game->getId());
    	$team1 = $list[0];
    	$team2 = $list[1];
    	if($team1['points'] < $team2['points'])
    	{
            echo "gano el equipo 2\n";
    		return $team2['team.teamId'];
    	}
    	else if($team1['points'] > $team2['points'])
    	{
            echo "gano el equipo 1\n";
    		return $team1['team.teamId'];
    	}
    	return null;
    }

	private function checkGame(&$gameId)
    {				
		//$duels = $game->getDuels()->findBy(array("active" => false, "finished" => true));
		$em = $this->co->get('doctrine')->getManager();
        $game = $em->getRepository("LiderBundle:Game")->find($gameId);
		$parameters = $this->co->get('parameters_manager')->getParameters();
		$duels = $em->getRepository('LiderBundle:Duel')->findBy(array("active" => true, "finished" => false, "game" =>$game));
        echo "estoy en el check game\n";
        echo count($duels);
		if(count($duels) == 0){
			$win = $this->checkWinTeam($game);
            echo "entre al count de duelos\n";
			if(!is_null($win))
			{
                echo "entre al equipo ganador";
				$team = $em->getRepository('LiderBundle:Team')->find($win);
                $game->setTeamWinner($team);
				$team->setPoints($team->getPoints() + $parameters['gamesParameters']['gamePoints']);
				$qm = $this->co->get('game_manager')->stopGame($game);
                $this->notificationPlayersDuelFinish($game->getTeamOne(), $game->getTeamTwo(), $team);
                $this->notificationPlayersDuelFinish($game->getTeamTwo(), $game->getTeamOne(), $team);
                $list = $em->getRepository("LiderBundle:Game")->findBy(array('active' => true, 'finished' => false));
                if(count($list) == 0)
                {
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
                                'body' => '<p>El nivel '.$game->getTournament()->getLevel().' ha finalizado. Por favor inicie el siguiente nivel</p>'
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

    private function notificationPlayersDuelFinish($team, $vs, $win)
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
    	$list = $dm->getRepository('LiderBundle:QuestionHistory')->findPlayersDontPlay($team->getId(), $game->getId());
    	$player = null;
    	if(count($team->getPlayers()) == count($list))
    	{
    		$random = rand(0, count($list)-1);
    		$players = $team->getPlayers();
    		$player = $players[$random];
    	}
    	else{

    		foreach($team->getPlayers() as $play)
    		{
    			$found = false;
    			foreach($list as $pla)
    			{
    				if($player->getId() == $pla['player.playerId'])
    				{
    					$found = true;
    					break;
    				}

    			}
    			if(!$found)
    			{
    				$player = $play;
    				break;
    			}
    		}
    	}
    	return $player;
    }

    private function generateExtraDuel($team1, $team2, $game)
    {
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
        $duel->tournament($game->getTournament());
        $duel->setExtraDuel(true);
        $countQuestion = $params['gamesParameters']['countQuestionDuelExtra'];
        $gameManager->generateQuestions($countQuestion, $duel);
        $em->persist($duel);
        $em->flush();
        $this->notificationExtraDuel($player1, $$team2, $player2);
        $this->notificationExtraDuel($player2, $$team1, $player1);
    }

    private function notificationExtraDuel($player, $teamvs, $playervs)
    {
        $gearman = $this->co->get('gearman');
        $body = 'Se ha generado un duelo definitivo entre tu equipo y el equipo '.$teamvs->getName().', y tu has sido el seleccionado para jugarlo contra '.$playervs->getName().' '.$playervs->getLastname();
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', json_encode(array(
            'subject' => 'Extra Duelo',
            'to' => $player->getEmail(),
            'viewName' => 'LiderBundle:Templates:emailnotification.html.twig',
            'body' => array(
                'title' => 'Tienes un Duelo Extra',
                'subjectMessage' => 'Se ha generado el duelo de desempate',
                'body' => $body
            )
        )));
    }
}