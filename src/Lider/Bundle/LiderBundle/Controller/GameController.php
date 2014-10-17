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

    /**
     * Obeter los duelos de un juego
     */
    public function getGameDuelsAction($gameId)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $duels = $em->getRepository('LiderBundle:Duel')->getArrayEntityWithOneLevel(array('game' => $gameId));        

        return $this->get("talker")->response($duels);   
    }

    // public function generateGameAction()
    // {
    //     $request = $this->get("request");
    //     $data = $request->getContent();
        
    //     if(empty($data))
    //         throw new \Exception("No data");        

    //     $data = json_decode($data, true);        
    //     $tournamentId = $data['tournamentId'];
    //     $pm = $this->get('parameters_manager');
    //     $params = $pm->getParameters();
    //     $interval = $params['gamesParameters']['countQuestionDuel'];
    
    //     $this->get('game_manager')->generateGame($tournamentId, $interval);

    //     // $this->get('game_manager')->generateGame(3, 7);
    //     return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    // }

    public function getGamesByGroupAction($tournament = null){
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('LiderBundle:Group');
        $list = $repo->findGamesByGroup($tournament);
        return $this->get("talker")->response(array('count' => count($list), 'data' => $list));
    }

    public function generateDuelAction()
    {
        $request = $this->get("request");

        $this->get('game_manager')->generateDuel(725, 2);

        return $this->get("talker")->response(array());
    }    

    public function stopGamesAction()
    {
        $this->get('game_manager')->stopGames();

        return $this->get("talker")->response(array());
    }

    public function startGamesAction()
    {
        $this->get('game_manager')->startGames();

        return $this->get("talker")->response(array());
    }

    public function stopDuelsAction()
    {
        $this->get('game_manager')->stopDuels();

        return $this->get("talker")->response(array());
    }    

    public function stopGameManualAction($gameId) {
    	
    	$gearman = $this->get('gearman');
		$result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkerchequear~stopGameManual', json_encode(array(
            'gameId' => $gameId            
        )));

		return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }
    
    
    public function sendNotificationByGameAction($gameId) {
    	
    	$gearman = $this->get('gearman');
    	$result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkerchequear~sendNotificationPlayersGameFinish', json_encode(array(
    			'gameId' => $gameId
    	)));
    	
    	return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));  	
    }

    public function getPointsGameAction($gameId)
    {
        $em = $this->get('doctrine')->getManager();
        // $list = $dm->getRepository("LiderBundle:QuestionHistory")->findTeamPointsByGame($gameId);
        $list = $em->getRepository("LiderBundle:Duel")->getDuelsByGame($gameId);
        $countT1 = 0;
        $countT2 = 0;
        $team1 = $list[0]->getPlayerOne()->getTeam();
        $team2 = $list[0]->getPlayerTwo()->getTeam();
        foreach($list as $duel)
        {
            echo "puntos del equipo ".$duel->getPlayerOne()->getName()." ".$duel->getPointOne()."\n";
            echo "puntos del equipo ".$duel->getPlayerTwo()->getName()." ".$duel->getPointTwo()."\n";
            if($duel->getPointOne() > $duel->getPointTwo())
            {
                $countT1++;
            }
            elseif($duel->getPointOne() < $duel->getPointTwo()){
                $countT2++;
            }
        }
        $return = array(
            "teamOne" => array("points" => $countT1, "team" => $team1->getId()),
            "teamTwo" => array("points" => $countT2, "team" => $team2->getId()),
        );
        return $this->get("talker")->response($return);
    }
    
}
