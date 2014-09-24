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
			echo "entro";
			$this->co->get('game_manager')->stopDuel($duel);
			$this->checkGame($duel->getGame());
		}

    }

	private function checkGame($game)
    {				
		//$duels = $game->getDuels()->findBy(array("active" => false, "finished" => true));
		$em = $this->co->get('doctrine')->getManager();
		$duels = $em->getRepository('LiderBundle:Duel')->findBy(array("active" => false, "finished" => true, "game" =>$game));
		if(count($duels) == 0){
			$qm = $this->co->get('game_manager')->stopGame($game);
		}
		
    }    
}