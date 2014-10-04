<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lider\Bundle\LiderBundle\Entity\Team;
use Lider\Bundle\LiderBundle\Entity\Group;

class GroupController extends Controller
{
    public function getName(){
    	return "Group";
    }
 
    /**
     * Generar grupos 
     */
 	public function generateGroupAction() {  

 		$em = $this->getDoctrine()->getEntityManager();   		
    	$request = $this->get("request");
    	
    	$min = $request->get('min');
    	$max = $request->get('max');    	

		$data = array();
    	$totalGroups = 0;

		$teams = $em->getRepository("LiderBundle:Team")->findBy(array('deleted' => false));		

		$t = array();
		foreach ($teams as $value) {
			$tem = array();
			
			$tem['name'] = $value->getName(); 
			$tem['id'] = $value->getId();	
			
			$t[]= $tem;
		}

		$group = $this->createGroup($t, $max, $min);
    	
    	$rec = array(
    		"totalGroups" => count($group),    		
    		"groups" => $group
    	);

		return $this->get("talker")->response($rec);
 	}

 	/**
 	 *  Crear grupos con sus equipos
 	 */
 	private function createGroup($teams, $max, $min){

 		$countTeams = count($teams); 		
 		$c = 0;

 		for ($i = $max; $i >= $min; $i--) {
    		$a = $countTeams % $i;    		
    		if($a == 0 || ($a >= $min && $a <= $max)){
    			$c = $i;
    			break;
    		}
    	}

   		if($c == 0){
    		for ($i = $min; $i <= $max; $i++) {
    			$a = $countTeams % $i;
    			if($c==0){
    				$c = $i;
    			}else if($a<$c){
    				$c = $i;
    			}
    		}
    	}
    	
    	$countGroup = $countTeams/$c;
    	$groups = array();
    	$subTeams = $teams;
    	$count = 0;
    	for ($j = 0; $j < $countGroup; $j++) {
    		$countSubTeams = count($subTeams);    		
    		if($countSubTeams >= $min){
    			$group = array(
    					'name' => 'GRUPO '.($j+1),
    					'countTeams'=> 0,    					
    					'teams' => array()
    			);
    			
    			for ($i = 0; $i < $c; $i++) {
    				$sp = $subTeams;
    			
    				$found = false;
    				while (count($sp) > 0 && $found == false) {
    		    
    					$pos = rand(1, count($sp)) -1;
    					$team = $sp[$pos];

    					if(count($group['teams']) > 0){
    						$splayer = $group['teams'][0];

    							$group['teams'][] = $team;
    							
    							$this->removeItem($subTeams, $team['id']);
    							$found = true;

    						array_splice($sp, $pos, 1);
    					}else{
    						$group['teams'][] = $team;

    						$this->removeItem($subTeams, $team['id']);
    						$found = true;
    						
    					}
    				}
    			}

    			$group['countTeams'] = count($group['teams']);
    			 
    			if(count($group['countTeams']) > 0){

    				$groups[] = $group;
    				$count++;
    			}
    		}
    		
    	}

    	return $groups;    	
 	}

    private function removeItem(&$array, $id) {
    	
    	foreach ($array as $key => $value) {
    		if($value['id'] == $id){
    			
    			array_splice($array, $key, 1);
    			break;
    		}
    	}
    }

    /**
     * Guardar grupos en BD
     */
    public function saveGroupAction(){
        
        $em = $this->getDoctrine()->getEntityManager(); 
        $request = $this->get("request");
        $data = $request->getContent();
        $data = json_decode($data, true);
        

        $tournamentId = $data['tournament'];

        $tournament = $em->getRepository("LiderBundle:Tournament")->findOneBy(array("id" => $tournamentId, "deleted" => false));
		$tournamentGroups = $em->getRepository("LiderBundle:Group")->findBy(array("tournament" => $tournamentId, "deleted" => false));
        
        foreach ($tournamentGroups as $value) {
            $value->setDeleted(true);
        }
        $em->flush();

        
        foreach ($data['groups'] as $val) {
            $group = new Group();
            $group->setName($val['name']);
            $group->setTournament($tournament);
            
            foreach ($val['teams'] as $v) {
                $team = $em->getRepository("LiderBundle:Team")->findOneBy(array("id" => $v['id'], "deleted" => false));
                if(!$team)
                    throw new \Exception("Player no found");

                $team->setGroup($group);
            }
            $em->persist($group);
            
        }
        

        $em->flush();

        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

    public function getGroupPositionAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        //$dm = $this->get('doctrine_mongodb')->getManager();
        $repo = $em->getRepository("LiderBundle:Group");
        $user = $this->container->get('security.context')->getToken()->getUser();
        $tournamentId = $user->getTeam()->getTournament()->getId();

        $gameRepo = $em->getRepository("LiderBundle:Game");

        $list = $repo->findBy(array('tournament' => $tournamentId, "deleted" => false));
        $l = array();
        foreach ($list as $group) {
            $teams = array();
            $g = array(
                "id" => $group->getId(),
                "name" => $group->getName(),
                "teams" => array()
            );
            foreach ($group->getTeams() as $team) {
                $teams[]= $team->getId();
            }
            $games = $gameRepo->getTeamPositions($teams);
            foreach ($group->getTeams() as $team) {
                $ls = array(
                    'id' => $team->getId(),
                    'name' => $team->getName(),
                    'points' => $team->getPoints(),
                    'total' => 0,
                    'win' => 0,
                    'loose' => 0,
                );
                foreach ($games as $game) {
                    if($game->getTeamOne()->getId() == $team->getId() || $game->getTeamTwo()->getId() == $team->getId()){
                        $ls['total']++;
                        if($game->getTeamWinner()){
                            if($game->getTeamWinner()->getId() == $team->getId()){
                                $ls['win']++;
                            }else{
                                $ls['loose']++;
                            }
                        }
                    }
                }
                $g["teams"][] = $ls;
               
            }
            $l[] = $g;
        }

        $orderBy = function($data, $field){
            $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
            usort($data, create_function('$a, $b', $code));
            return $data;
        };
        foreach ($l as $value) {
           $teams = $orderBy($value["teams"], "points");
           $list=array();
            foreach ($teams as $value) {
                array_unshift($list, $value);
            }
            $value["teams"] = $teams;
        }

        return $this->get("talker")->response(array("total" => count($l), "data" => $l));
    }

    public function notificationGroupAction()
    {
        $gearman = $this->get('gearman');
        $request = $this->get("request");
        $tournament = $request->get('tournamentId');
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendNotificationGroup', json_encode(array(
                'tournament' => $tournament,
            )));
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

}
