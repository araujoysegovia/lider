<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class ParametersManager {

	private $pathParameters = '/var/www/lider/src/Lider/Bundle/LiderBundle/Resources/config/gameParameters.yml';
    
    /**
     * Setear los parametros de configuración para el juego
     */
    public function setParameters(array $params){    	
    	$yaml = new Parser();

    	try{
    		$parameters = $yaml->parse(file_get_contents($this->pathParameters));	

		 	foreach ($params as $key => $value) {
		 		if(!is_null($value)){
		 			$parameters['gamesParameters'][$key] = $value;
		 		}
		 	}			 	

		 	$dumper = new Dumper();
			$yaml = $dumper->dump($parameters, 2);
			file_put_contents($this->pathParameters, $yaml);

    	}catch (ParseException $e) {
		    printf("Unable to parse the YAML string: %s", $e->getMessage());
		}
		 
		// $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }

    /**
     * Obtener parametros del yml
     */
    public function getParameters(){

		$yaml = new Parser();


		if(!file_exists($this->pathParameters)){
			file_put_contents($this->pathParameters, "");
		}

		try {
		    $parameters = $yaml->parse(file_get_contents($this->pathParameters));		   
		} catch (ParseException $e) {
		    printf("Unable to parse the YAML string: %s", $e->getMessage());
		}

    	return $parameters;
    }


}
	