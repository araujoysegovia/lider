<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\ControllerController;
use Symfony\Component\HttpFoundation\Request;

class PlayerController extends Controller
{
    public function getName(){
    	return "Player";
    }
}
