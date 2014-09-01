<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Lider\Bundle\LiderBundle\Lib\Normalizer;

class QuestionManager {
	
	private $em;
	private $dm;
	
	public function __construct($em, $dm) {
		$this->em = $em;
		$this->dm = $dm;
	}
	
	/**	
	 * Obtener preguntas para el duelo
	 * @param int $count, cantidad de preguntas que debe devolver
	 * @param string $duelId, 
	 * @return $questions
	 */
	function generateQuestions($count, $duelId = null) {
		
		$arr = array();
		if(!(is_null($duelId))){
			$duel = $this->em->getRepository("LiderBundle:Duel")->findOneBy(array("id" =>$duelId, "deleted" => false));
			
			$questionsDuel = $dm->createQueryBuilder('LiderBundle:DuelQuestion')	  
	    			            ->field('gameId')->equals($duel->getGame()->getId())
							    ->getQuery()
								->execute();
			
			foreach ($questionsDuel->toArray() as $value) {
				$arr[] = $value->getQuestionId();
			}
		}
		//$arr[] = 100; 
		$questionList = $this->em->getRepository("LiderBundle:Question")->getQuestionListNotIn($arr);

		$c = count($questionList);

		$questions = array();
		for ($i = 0; $i < $count && $c > 0; $i++) {
			$pos = rand(1, $c);
			
			$ql = $questionList[$pos-1];
			$ass = $ql['answers'];
			$arrayAss = array();
			foreach ($ass as $value) {
				$arrayAss[] = array(
					'id' => $value['id'],
					'answer' => $value['answer']
				);
			}
			$ql['answers'] = $arrayAss;
			
			$questions[] = $ql;
		}
		
		return $questions;
		
	}
}