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

        $yaml = new Parser();
        
        $timeQuestionPractice = $data['timeQuestionPractice'];
        $timeQuestionDuel = $data['timeQuestionDuel'];
        $timeGame = $data['timeGame'];
        $timeDuel = $data['timeDuel'];

        $parameters = $this->get('parameters_manager')->setParameters($timeQuestionPractice, $timeQuestionDuel, $timeGame, $timeDuel);

		 
		return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }

    /**
     * Obtener parametros de configuracion desde el archivo .yml
     */    
    public function getParametersAction(){

        $parameters = $this->get('parameters_manager')->getParameters();

    	return $this->get("talker")->response($parameters);
    }
}
