<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends Controller
{
    public function getName(){
    	return "Group";
    }
 
 	public function generateGroupAction() {  

    	$request = $this->get("request");
    	
    	$minPlayersAmount = $request->get('min');
    	$maxPlayersAmount = $request->get('max');
    	
    	$cities = $em->getRepository("LiderBundle:Office")->getCities();
 	}
}
