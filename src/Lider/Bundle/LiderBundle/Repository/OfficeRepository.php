<?php

namespace Lider\Bundle\LiderBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class OfficeRepository extends MainRepository
{
	
	/**	
	 * 
	 * @param unknown $id
	 */
	public function getCities() {		
		
		$em = $this->getEntityManager();
		$query =  $em->createQuery('SELECT DISTINCT o.city  FROM LiderBundle:Office o WHERE o.deleted = false');	
		$data = $query->getArrayResult();
		
		//print_r($data);
		return $data;
	}
			
		
}