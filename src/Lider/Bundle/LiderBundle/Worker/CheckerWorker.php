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

		$em = $this->co->get('doctrine')->getManager();

		$duel = $em->getRepository('LiderBundle:Duel')->find($duelId);
		$questions = $em->getRepository('LiderBundle:DuelQuestion')
						->findBy(array("duel" => $duelId, "deleted" =>false));

		if(!$duel){
			return ;
		}

		$questionIds = array();
		foreach ($questions as $key => $question) {
			$questionIds[] = $question->getId();
		}

		if(count($questionIds) == 0){
			return ;
		}

		$dm = $this->co->get('doctrine_mongodb')->getManager();
		$qh = $dm->getRepository('LiderBundle:QuestionHistory')->getMissingQuestionByDuel($duelId, $questionIds);

		if(count($qh->toArray()) == 0){
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