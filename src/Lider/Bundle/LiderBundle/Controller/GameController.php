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
    	
    	echo "entro game Controller";
    	$pathParameters = '/var/www/lider/src/Lider/Bundle/LiderBundle/Resources/config/gameParameters.yml';
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
		 
		return $this->get("talker")->response(array()); 
    }

    /**
     * Obtener parametros de configuracion desde el archivo .yml
     */    
    public function getParametersAction(){

		$pathParameters = '/var/www/lider/src/Lider/Bundle/LiderBundle/Resources/config/gameParameters.yml';
		$yaml = new Parser();

		try {
		    $parameters = $yaml->parse(file_get_contents($pathParameters));		   
		} catch (ParseException $e) {
		    printf("Unable to parse the YAML string: %s", $e->getMessage());
		}

    	return $this->get("talker")->response($parameters);
    }
}
