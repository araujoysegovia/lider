<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TournamentController extends Controller
{
    public function getName(){
    	return "Tournament";
    }

    /**
     * Funcin que realiza alguna accion antes de guardar una entidad.
     */
    protected function beforeSave(&$Entity) {
        $Entity->setLevel(1);
    }

    /**
     * Buscar torneos activos
     */
    public function activeTournamentAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$tournaments = $em->getRepository("LiderBundle:Tournament")->findBy(array("active" => true, "deleted" => false));
//     	$entity = $em->getRepository("LiderBundle:Tournament")->activeTournament();
    	if(!$tournaments)
    		throw new \Exception("Tournament no found");

        $array = array();
        foreach ($tournaments as $tournament) {
            $arr = array();
            

            $teams = $em->getRepository("LiderBundle:Team")
                    ->findBy(array("tournament" => $tournament->getId(), "deleted" => false));

            $arr['id'] = $tournament->getId();
            $arr['name'] = $tournament->getName();
            $arr['level'] = $tournament->getLevel();
            $arr['teams'] = $teams;
            $array[] = $arr;
        }

       
    	return $this->get("talker")->response($array);
        //return $this->get("talker")->response($tournaments);
    }

    public function activeLevelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get("request");
        $data = $request->getContent();
        
        if(empty($data))
            throw new \Exception("No data");        

        $data = json_decode($data, true);        
        $tournamentId = $data['tournamentId'];
        $date = null;
        if(array_key_exists('date', $data))
        {
            $date = $data['date'];
            $date = new \DateTime($date);
        }
        
        $tournament = $em->getRepository('LiderBundle:Tournament')->find($tournamentId);
        $tournament->setEnabledLevel(true);
        $em->flush();
        $pm = $this->get('parameters_manager');
        $params = $pm->getParameters();
        $interval = $params['gamesParameters']['timeGame'];
        $this->get('game_manager')->generateGame($tournamentId, $interval, $date);

        // $this->get('game_manager')->generateGame(3, 7);
        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }

    public function tournamentTemsAction($tournamentId)
    {
        $teams = $em->getRepository("LiderBundle:Team")
                    ->findBy(array("tournament" => $tournamentId, "deleted" => false));

        if(!$teams)
            throw new \Exception("Entity no found");

        
        return $this->get("talker")->response($teams);
    }

    public function getOnlyTournamentsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository("LiderBundle:Tournament")->findBy(array());
        if(!$entity)
            throw new \Exception("Entity no found");

        return $this->get("talker")->response($entity);
    }
}
