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
	private $co;

	public function __construct($co) {
		$this->co = $co;
		$this->em = $co->get('doctrine')->getManager();
		$this->dm = $co->get('doctrine_mongodb')->getManager();
		
	}
	
	/**	
	 * Obtener preguntas para el duelo
	 * @param int $count, cantidad de preguntas que debe devolver
	 * @param string $duelId, 
	 * @return $questions
	 */
	function getQuestions($count, $duel = null) {
		
		$arr = array();
		if(!(is_null($duel))){
			$user = $this->co->get('security.context')->getToken()->getUser();
			$questionList = $this->getMissingQuestionFromDuel($duel, $user);
		}else{
			//$arr[] = 100; 
			$questionList = $this->em->getRepository("LiderBundle:Question")->getQuestionList();
		}

		$c = count($questionList);
		//echo $c;
		$questions = array();
		for ($i = 0; $i < $count && $c > 0; $i++) {
			$pos = rand(1, $c);
			
			$ql = $questionList[$pos-1];
			$ass = $ql['answers'];
			$arrayAss = array();
			foreach ($ass as $value) {
				$arrayAss[] = array(
					'id' => $value['id'],
					'answer' => $value['answer'],
					'oa' => $value['selected'],
					'help' => $value['help']
				);
			}
			$ql['answers'] = $arrayAss;
			
			$questions[] = $ql;
			array_splice($questionList, ($pos-1), 1);
			$c = count($questionList);
		}
		
		//print_r($questions);
		return $questions;		
	}

	/**
	 * Generar preguntas y devolver como entidad
	 */
	function generateEntityQuestions($count, $duel = null) {
		
		$arr = array();
		if(!(is_null($duel))){
			//$duel = $this->em->getRepository("LiderBundle:Duel")->findOneBy(array("id" =>$duelId, "deleted" => false));			
			$questionsDuel = $this->dm->createQueryBuilder('LiderBundle:DuelQuestion')	  
	    			            ->field('gameId')->equals($duel->getGame()->getId())
							    ->getQuery()
								->execute();
			
			foreach ($questionsDuel->toArray() as $value) {
				$arr[] = $value->getQuestionId();
			}
		}
		//$arr[] = 100; 
		$questionList = $this->em->getRepository("LiderBundle:Question")->getQuestionListNotIn($arr, false);

		$c = count($questionList);

		$questions = array();
		for ($i = 0; $i < $count && $c > 0; $i++){			
			$pos = rand(1, $c);
			
			$ql = $questionList[$pos-1];			
			$questions[] = $ql;
			array_splice($questionList, ($pos-1), 1);
			$c = count($questionList);
		}
		
		return $questions;
		
	}	


	public function getMissingQuestionFromDuel($duel, $user)
	{			
		$listHistory = $this->dm->getRepository("LiderBundle:QuestionHistory")
								->getQuestionFinishedForDuel($user, $duel);
		
		$listId = array();
		foreach ($listHistory->toArray() as $key => $value) {								
			$listId[] = $value->getQuestion()->getQuestionId();
		}

		//print_r($listId);

		$dq = $this->em->getRepository("LiderBundle:Question")->getQuestionListFromDuel($listId, $duel);
		//echo count($dq);
		$questionList = array();
		foreach ($dq as $key => $value) {
			//print_r($value);
			$questionList[] = $value['question'];
		}			

		//print_r($questionList);
		return $questionList;
	}
}