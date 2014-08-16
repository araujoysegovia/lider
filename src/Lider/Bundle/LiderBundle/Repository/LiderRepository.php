<?php
namespace Lider\Bundle\LiderBundle\Repository;

class LiderRepository extends MainRepository
{
	public function findPlayersFromTeam($teamId){
		$query = $this->createQueryBuilder("t")
		->select("")
		->leftJoin("e.person", "pe")
		->where("e.deleted = FALSE AND pe.deleted = FALSE AND ide.deleted = FALSE AND ofi.deleted = FALSE AND eco.deleted = FALSE AND tw.deleted = FALSE AND it.deleted = FALSE");
		if($filter)
		{
			$counter = 1;
			foreach($filter as $fil)
			{
				$this->setFilterToQuery($query, $fil, $counter);
				$counter++;
			}
		}
		$sql = $query->getQuery();
		$data = $sql->getArrayResult();
		$count = count($data);
		if(!is_null($start) && !is_null($limit))
        {
            $this->aplyPagination($data, $count, $start, $limit);
        }
		return array(
			"total" => $count,
			"data" => $data
		);
	}
}
?>