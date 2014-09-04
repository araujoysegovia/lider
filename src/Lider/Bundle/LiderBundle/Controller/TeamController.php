<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lider\Bundle\LiderBundle\Document\Image;

class TeamController extends Controller
{
    public function getName(){
    	return "Team";
    }
    
    public function setImageAction($id) {
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository("LiderBundle:Team")->findOneBy(array("id" => $id, "deleted" => false));
    	if(!$entity)
    		throw new \Exception("Entity no found");
    	
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$request = $this->get("request");

    	$uploadedFile = $request->files->get('imagen');
    	$className = self::$NAMESPACE.$this->getName();
    	
    	$image = new Image();
    	$image->setName($uploadedFile->getClientOriginalName());
    	$image->setFile($uploadedFile->getPathname());
    	$image->setMimetype($uploadedFile->getClientMimeType());
		$image->setEntity($className);
		$image->setEntityId($id);
		
    	$dm->persist($image);
    	$dm->flush();
    	
    	$entity->setImage($image->getId());
    	
    	$em->flush();
    	
    	return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }
 
    
    public function generateTeamAction() {
    	
    	$request = $this->get("request");
    	
    	$minPlayersAmount = $request->get('min');
    	$maxPlayersAmount = $request->get('max');
    	    	
    	$em = $this->getDoctrine()->getEntityManager();    	
		
    	$cities = $em->getRepository("LiderBundle:Office")->getCities();
    	
    	$data = array();
    	$totalTeams = 0;
    	$totalOut =0;
    	$totalPlayers=0;
    	for ($i = 0; $i < count($cities); $i++) {
    		$cityName = $cities[$i]["city"];
    		$players = $em->getRepository("LiderBundle:Player")->playersForCity($cityName);
    		$team = $this->createTeams($players, $minPlayersAmount, $maxPlayersAmount, $cityName);
    		$totalTeams+=$team["totalTeam"];
    		$totalOut+=$team["totalOut"];
    		$totalPlayers+=$team["totalPlayers"];
    		$data[$cityName] = $team;
    	}

    	$rec = array(
    		"totalTeam" => $totalTeams,
    		"totalOut" => $totalOut,
    		"totalPlayers" => $totalPlayers,
    		"cities" => $data
    	);
    	return $this->get("talker")->response($rec);
    }
    
    private function createTeams($players, $min, $max, $cityName){
    	
    	$countPlayers = count($players);
    	$c = 0;
    	
    	for ($i = $max; $i >= $min; $i--) {
    	
    		$a = $countPlayers % $i;
    		//echo "\n\n".$countPlayers."%".$i."=".$a;
    		if($a == 0 || ($a >= $min && $a <= $max)){
    			$c = $i;
    			break;
    		}
    	}
    	 
    	if($c == 0){
    		for ($i = $min; $i <= $max; $i++) {
    			$a = $countPlayers % $i;
    			if($c==0){
    				$c = $i;
    			}else if($a<$c){
    				$c = $i;
    			}
    		}
    	}
    	
    	
    	$countGroup = $countPlayers/$c;
    	$teams = array();
    	$subPlayers = $players;
    	$count = 0;
    	for ($j = 0; $j < $countGroup; $j++) {
    		$countSubplayers = count($subPlayers);
    		if($countSubplayers >= $min){
    			$team = array(
    					'name' => $cityName.' - Equipo '.($j+1),
    					'countPlayers'=> 0,    					
    					'players' => array()
    			);
    			 
    			for ($i = 0; $i < $c; $i++) {
    				$sp = $subPlayers;
    				//echo count($sp);
    				$found = false;
    				while (count($sp) > 0 && $found == false) {
    		    
    					$pos = rand(1, count($sp)) -1;
    					$player = $sp[$pos];
    					//echo "<br/>".count($sp);
    		    
    					if(count($team['players']) > 0){
    						$splayer = $team['players'][0];
//     						if($splayer['office']['city'] == $player['office']['city']){
    							$team['players'][] = $player;
    							$this->removeItem($subPlayers, $player['id']);
    							$found = true;
//     						}
    						//unset($sp[$pos-1]);
    						array_splice($sp, $pos, 1);
    					}else{
    						$team['players'][] = $player;
//     						$team['office'] = $player['office']['name'];
    						$this->removeItem($subPlayers, $player['id']);
    						$found = true;
    						//return new Response("<br/>".count($team));
    					}
    				}
    			}
    			$team['countPlayers'] = count($team['players']);
    			 
    			if(count($team['countPlayers']) > 0){
//     				$city =  $team['players'][0]['office']['city'];
//     				if(!array_key_exists($city, $teams)){
//     					$teams[$city] = array();
//     				}
    				$teams[] = $team;
    				$count++;
    			}
    		}
    	}
    	
    	$res = array(
    			'totalTeam' => $count,
    			'totalOut' => count($subPlayers),
    			'totalPlayers' => $countPlayers,
    			'totalPlayersByTeam' => $c,
    			'teams' => $teams,
    			'out' => $subPlayers        	
    	);
    	
    	return $res;
    	
    }
    
    private function removeItem(&$array, $id) {
    	foreach ($array as $key => $value) {
    		if($value['id'] == $id){
    			
    			array_splice($array, $key, 1);
    			break;
    		}
    	}
    }
}
