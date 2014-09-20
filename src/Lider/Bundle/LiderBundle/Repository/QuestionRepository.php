<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends MainRepository
{
	
	/**	
	 * Partidos ganados y partidos perdidos del jugador
	 * @param unknown $id
	 */
	public function getPlayerGamesInfo($playerId) {		
		$em = $this->getEntityManager();
		$query =  $em->createQuery('SELECT * FROM LiderBundle:Question  WHERE o.deleted = false');	
		$data = $query->getArrayResult();		
		
		return $data;
	}

	/**
	 * 
	 */
	public function getQuestionListNotIn(array $listId = array(), $asArray = true) {
		
		$query =  $this->createQueryBuilder('q')
						->select('q, r, c')
						->join('q.answers', 'r', 'WITH','r.deleted = false')
						->join('q.category', 'c', 'WITH','c.deleted = false')
						->where('q.deleted = false AND q.checked = true');
							
		if(!count($listId)==0) {		
			$query->andWhere('q.id NOT IN (:ids)')
				  ->setParameter('ids',$listId);
		}
		
		$query = $query->getQuery();
		//echo $query->getSQL()."<br/><br/>";

		if($asArray){
			return $query->getArrayResult();	
		}else{
			return $query->getResult();
		}
		
	}


	public function getQuestionList($asArray = true) {
		
		$query =  $this->createQueryBuilder('q')
						->select('q, r, c')
						->join('q.answers', 'r', 'WITH','r.deleted = false')
						->join('q.category', 'c', 'WITH','c.deleted = false')
						->where('q.deleted = false AND q.checked = true');
									
		$query = $query->getQuery();

		if($asArray){
			return $query->getArrayResult();	
		}else{
			return $query->getResult();
		}
		
	}

	public function getQuestionListFromDuel($duel, $asArray = true) {
		
		$repo = $this->getEntityManager()->getRepository('LiderBundle:DuelQuestion');
		$query =  $repo->createQueryBuilder('dq')
						->select('q, r, c, dq')
						->join('dq.question','q','WITH', 'q.deleted = false AND q.checked = true')
						->join('q.answers', 'r', 'WITH','r.deleted = false')
						->join('q.category', 'c', 'WITH','c.deleted = false')
						->where('dq.deleted = false AND dq.duel  =:duel')
						->setParameter('duel', $duel);
							
		$query = $query->getQuery();

		if($asArray){
			return $query->getArrayResult();	
		}else{
			return $query->getResult();
		}
		
	}	
	
}