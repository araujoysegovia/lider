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
    	$players = $em->getRepository("LiderBundle:Player")->getArrayEntityWithOneLevel(array("active" => true, "deleted" => false));
    	$players = $players['data'];
    	$countPlayers = count($players);
 		    	
    	$c = 0;

    	for ($i = $maxPlayersAmount; $i >= $minPlayersAmount; $i--) {
    		
    		$a = $countPlayers % $i;
    		echo "<br/>".$countPlayers."%".$i."=".$a;
    		if($a == 0 || ($a >= $minPlayersAmount && $a <= $maxPlayersAmount)){
    			$c = $i;
    			break;
    		}
    	}
    	
    	if($c == 0){
    		for ($i = $minPlayersAmount; $i <= $maxPlayersAmount; $i++) {
    			$a = $countPlayers % $i;
    			if($c==0){
    				$c = $a;					
    			}else if($a<$c){
    				$c = $i;
    			}
    		}
    	}
    	
    	//return new Response("jajaj");
    	$countGroup = $countPlayers/$c;
    	$teams = array();
    	$subPlayers = $players;
    	for ($j = 0; $j < $countGroup; $j++) {
    		$countSubplayers = count($subPlayers);
    		if($countSubplayers > $c){
	    		$team = array(
	    			'name' => 'Equipo '.($j+1),
	    			'countPlayers'=> 0,
	    			'office' => null,
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
	    					if($splayer['office']['id'] == $player['office']['id']){
	    						$team['players'][] = $player;
	    						$this->removeItem($subPlayers, $player['id']);	
	    						$found = true;
	    					}
	    					//unset($sp[$pos-1]);
	    					array_splice($sp, $pos, 1);
	    				}else{
	    					$team['players'][] = $player;
	    					$team['office'] = $player['office']['name'];
	    					$this->removeItem($subPlayers, $player['id']);
	    					$found = true;
	    					//return new Response("<br/>".count($team));
	    				}
	    			}  	    		    		
	    		}
	    		$team['countPlayers'] = count($team['players']);
	    		if(count($team['countPlayers']) > 0){
	    			$teams[] = $team; 
	    		}   
    		} 		
    	}    

    	$res = array(
    	    'totalTeam' => count($teams),
    		'totalOut' => count($subPlayers),
    		'totalPlayers' => $countPlayers,
    		'totalPlayersByTeam' => $c,
    		'teams' => $teams,
    		'out' => $subPlayers
    		
    		
    	);
    	
    	return $this->get("talker")->response($res);
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
