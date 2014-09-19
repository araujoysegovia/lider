<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class GameController extends Controller
{
    public function getName(){
    	return "Game";
    }


    /**
     * Setear los parametros de configuración para el juego
     */
    public function setParametersAction(){
    	
        $request = $this->get("request");
        $data = $request->getContent();
        
        if(empty($data))
            throw new \Exception("No data");        

        $data = json_decode($data, true);

        $parameters = $this->get('parameters_manager')->setParameters($data);

		 
		return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }

    /**
     * Obtener parametros de configuracion desde el archivo .yml
     */    
    public function getParametersAction(){

        $parameters = $this->get('parameters_manager')->getParameters();

    	return $this->get("talker")->response($parameters);
    }

    public function generateGameAction()
    {
        $this->get('game_manager')->generateGame(3, 7);

        return $this->get("talker")->response(array());
    }

    public function getGamesByGroupAction($tournament = null){
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('LiderBundle:Group');
        $list = $repo->findGamesByGroup($tournament);
        return $this->get("talker")->response(array('count' => count($list), 'data' => $list));
    }
}
