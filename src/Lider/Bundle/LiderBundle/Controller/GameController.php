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
        $request = $this->get("request");
        // $data = $request->getContent();
        
        // if(empty($data))
        //     throw new \Exception("No data");        

        // $data = json_decode($data, true);        
        // $tournamentId = $data['tournamentId'];
        // $interval = $data['interval'];
    
        // $this->get('game_manager')->generateGame($tournamentId, $interval);

        $this->get('game_manager')->generateGame(3, 7);

        return $this->get("talker")->response(array());
    }

    public function generateDuelAction()
    {
        $request = $this->get("request");

        $this->get('game_manager')->generateDuel(3);

        return $this->get("talker")->response(array());
    }    
}
