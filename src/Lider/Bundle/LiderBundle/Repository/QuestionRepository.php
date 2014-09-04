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

	public function getQuestionListNotIn(array $listId = array()) {
		
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
		
		return $query->getArrayResult();
	}
	
}