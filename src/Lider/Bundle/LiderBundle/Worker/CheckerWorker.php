<?php
namespace Lider\Bundle\LiderBundle\Worker;

use Mmoreram\GearmanBundle\Driver\Gearman;

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
			$this->checkGame($duel->getGame());
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
    		return $team2['team.teamId'];
    	}
    	else if($team1['points'] > $team2['points'])
    	{
    		return $team1['team.teamId'];
    	}
    	return null;
    }

	private function checkGame($game)
    {				
		//$duels = $game->getDuels()->findBy(array("active" => false, "finished" => true));
		$em = $this->co->get('doctrine')->getManager();
		$parameters = $this->co->get('parameters_manager')->getParameters();
		$duels = $em->getRepository('LiderBundle:Duel')->findBy(array("active" => false, "finished" => true, "game" =>$game));
		if(count($duels) == 0){
			$win = $this->checkWinTeam($game);
			if(!is_null($win))
			{
				$team = $em->getRepository('LiderBundle:Team')->find($win);
				$team->setPoints($team->getPoints() + $parameters['gamesParameters']['gamePoints']);
				$qm = $this->co->get('game_manager')->stopGame($game);
			}
		}
		
    }    
}