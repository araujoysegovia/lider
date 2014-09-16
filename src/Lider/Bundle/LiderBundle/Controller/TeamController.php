<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lider\Bundle\LiderBundle\Entity\Team;
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
 
    
    /**
     * Generar equipos
     */
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
    
    /**
     * Crear equipos por ciudades
     */
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
    				$found = false;
    				while (count($sp) > 0 && $found == false) {
    		    
    					$pos = rand(1, count($sp)) -1;
    					$player = $sp[$pos];

    					if(count($team['players']) > 0){
    						$splayer = $team['players'][0];
    							$team['players'][] = $player;
    							$this->removeItem($subPlayers, $player['id']);
    							$found = true;
    						array_splice($sp, $pos, 1);
    					}else{
    						$team['players'][] = $player;

    						$this->removeItem($subPlayers, $player['id']);
    						$found = true;
    					}
    				}
    			}
    			$team['countPlayers'] = count($team['players']);
    			 
    			if(count($team['countPlayers']) > 0){
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

    /**
     * Guardar equipos en BD
     */
    public function saveTeamAction(){
        
        $em = $this->getDoctrine()->getEntityManager(); 
        $request = $this->get("request");
        $data = $request->getContent();
        $data = json_decode($data, true);
        $cities = $data['cities'];
        $tournamentId = $data['tournament'];

        $tournament = $em->getRepository("LiderBundle:Tournament")->findOneBy(array("id" => $tournamentId, "deleted" => false));

        foreach ($tournament->getTeams()->toArray() as $value) {
            $value->setDeleted(true);
        }
        $em->flush();

        foreach ($cities as $value) {
            foreach ($value['teams'] as $val) {
                $team = new Team();
                $team->setName($val['name']);
                $team->setTournament($tournament);
                
                foreach ($val['players'] as $v) {
                    $player = $em->getRepository("LiderBundle:Player")->findOneBy(array("id" => $v['id'], "deleted" => false));
                    if(!$player)
                        throw new \Exception("Player no found");

                    $player->setTeam($team);
                }
                $em->persist($team);
                
            }
        }

        $em->flush();

        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

}
