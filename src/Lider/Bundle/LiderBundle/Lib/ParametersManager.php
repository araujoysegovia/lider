<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class ParametersManager {


    /**
     * Setear los parametros de configuración para el juego
     */
    public function setParameters($timeQuestionPractice, $timeQuestionDuel, $timeGame, $timeDuel){
    	    	
    	$pathParameters = '/var/www/lider/src/Lider/Bundle/LiderBundle/Resources/config/gameParameters.yml';
    	$yaml = new Parser();

    	try{
    		$parameters = $yaml->parse(file_get_contents($pathParameters));	

    		if(!is_null($timeQuestionPractice))
		    	$parameters['gamesParameters']['timeQuestionPractice'] = $timeQuestionPractice;		
		    	
		    if(!is_null($timeQuestionDuel))	    
		 		$parameters['gamesParameters']['timeQuestionDuel'] = $timeQuestionDuel;		    	
		 		
		    if(!is_null($timeGame))	    
		 		$parameters['gamesParameters']['timeGame'] = $timeGame;		

		    if(!is_null($timeDuel))	    
		 		$parameters['gamesParameters']['timeDuel'] = $timeDuel;				 	

		 	$dumper = new Dumper();
			$yaml = $dumper->dump($parameters, 2);
			file_put_contents($pathParameters, $yaml);

    	}catch (ParseException $e) {
		    printf("Unable to parse the YAML string: %s", $e->getMessage());
		}
		 
		// $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }

    public function getParameters(){

		$pathParameters = '/var/www/lider/src/Lider/Bundle/LiderBundle/Resources/config/gameParameters.yml';
		$yaml = new Parser();

		if (!file_exists($pathParameters)){
			file_put_contents($pathParameters, "");
		}

		try {
		    $parameters = $yaml->parse(file_get_contents($pathParameters));		   
		} catch (ParseException $e) {
		    printf("Unable to parse the YAML string: %s", $e->getMessage());
		}

    	return $parameters;
    }


}
	