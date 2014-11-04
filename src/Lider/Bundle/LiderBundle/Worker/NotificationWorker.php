<?php
namespace Lider\Bundle\LiderBundle\Worker;

use Mmoreram\GearmanBundle\Driver\Gearman;

/**
 * @Gearman\Work(
 *     name = "notification",
 *     description = "Worker to Send Notifications",
 *     defaultMethod = "doBackground",
 *     service="notificationWorker"     
 * )
 */
class NotificationWorker
{
    private $co;

    private $from = 'lider@araujoysegovia.com';

    // private $em

    public function __construct($co){
        $this->co = $co;
        // $this->em = $em;
    }

    /**
     * Send Email
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "sendEmail",
     *     description = "Send an Email"
     * )
     */
	public function sendEmail(\GearmanJob $job)
    {
        $data = json_decode($job->workload(),true);
        //print_r($data);
        try{
	        $notificationService = $this->co->get("notificationService");
	        //echo "\n\n\n\nVoy a consultar ".$data['to'];
	        $player = $this->co->get('doctrine')->getManager()->getRepository("LiderBundle:Player")->findOneByEmail($data['to']);
	        if(!$player)
	        	return;
	        
	        echo "\n\nVoy a enviar correo a ".$player->getName()." ".$player->getName();
	        $team = $player->getTeam();
//         $to = $this->getEmailFromTeamId($team->getId());
       

            $send = $notificationService->sendEmail($data['subject'], $this->from, $data['to'], null, $data['viewName'], $data['content']);
            echo "\n\nMensaje Enviado a: ";
            print_r($data['to']);

        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    private function getEmailFromTeamId($teamId)
    {
        foreach($this->to as $key => $value)
        {
            if(in_array($teamId, $value)){
                return $key;
            }
        }
    }

    /**
     * Send Email when a team is created
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "sendNotificationTeam",
     *     description = "Send an Email when need a notification for the members of the team"
     * )
     */
    public function sendNotificationTeam(\GearmanJob $job){
        $data = json_decode($job->workload(),true);
        $tournamentId = $data['tournament'];
        // print_r($team->getId());
        $em = $this->co->get('doctrine')->getManager();
        $notificationService = $this->co->get("notificationService");
        $repoTeam = $em->getRepository("LiderBundle:Team");
        $list = $repoTeam->findBy(array("tournament" => $tournamentId, "deleted" => FALSE));
        $subject = "Este es tu Equipo!!!";
        if($list){
            foreach($list as $team){
                $to = array();
                $content = array(
                    "teamImage" => $team->getImage(),
                    "title" => $team->getName(),
                );
                $members = array();
                $players = $team->getPlayers();
                foreach($players as $player)
                {
                     $to[] = $player->getEmail();

                    $members[$player->getId()]['image'] = $player->getImage();
                    $members[$player->getId()]['name'] = $player->getName().' '.$player->getLastname();
                }
                $content['members'] = $members;
//                 $to = $this->getEmailFromTeamId($team->getId());
                try{

                    $send = $notificationService->sendEmail($subject, $this->from, $to, null, "LiderBundle:Templates:notificationteam.html.twig", $content);
                    echo "Mensaje Enviado de equipo";
                }catch(\Exception $e){
                    echo $e->getMessage();
                }
                
            }
            
        }
    }



    /**
     * Send Email when a team is created
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "sendNotificationDuel",
     *     description = "Send an Email when need a notification for duels active"
     * )
     */
    public function sendNotificationDuel(\GearmanJob $job){
        $data = json_decode($job->workload(),true);
        $tournamentId = $data['tournament'];
        // print_r($team->getId());
        $em = $this->co->get('doctrine')->getManager();
        
        $repoDuel = $em->getRepository("LiderBundle:Duel");
        $list = $repoDuel->findBy(array("tournament" => $tournamentId, "deleted" => FALSE, "active" => true, "finished" => false));
        
        if($list){
            foreach($list as $duel){
                $playerOne = $duel->getPlayerOne();
                $playerTwo = $duel->getPlayerTwo();
                $this->sendDuelNotification($playerOne, $playerTwo->getTeam(), $playerTwo, $duel);
                $this->sendDuelNotification($playerTwo, $playerOne->getTeam(), $playerOne, $duel);
            }
            
        }
    }

    private function sendDuelNotification($player, $teamvs, $playervs, $duel)
    {
        $notificationService = $this->co->get("notificationService");
        $subject = "Duelo generado!!!";
        $body = 'Hola <b>'.$player->getName().' '.$player->getLastname().'</b><br><br> Se ha generado un duelo entre tu equipo y el equipo '.$teamvs->getName().', y tu has sido el seleccionado para jugarlo contra <b>'.$playervs->getName().' '.$playervs->getLastname().'</b>';
        $content = array(
            "title" => "Tienes un Duelo",
            'subjectMessage' => 'Se ha generado tu duelo',
            'body' => $body,
            'duelId' => base64_encode($duel->getId())
        );
        try{
            $send = $notificationService->sendEmail($subject, $this->from, $player->getEmail(), null, "LiderBundle:Templates:duelnotification.html.twig", $content);
            echo "Mensaje Enviado de duelo a ".$player->getEmail();
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * Send Email when a team is created
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "sendNotificationGroup",
     *     description = "Send an Email when need a notification for the members of the team"
     * )
     */
    public function sendNotificationGroup(\GearmanJob $job){
        $data = json_decode($job->workload(),true);
        $tournamentId = $data['tournament'];
        $em = $this->co->get('doctrine')->getManager();
        $notificationService = $this->co->get("notificationService");
        $repo = $em->getRepository("LiderBundle:Group");
        $list = $repo->findBy(array("tournament" => $tournamentId, "deleted" => FALSE));
        $subject = "Este es tu Grupo!!!";
        
        if($list){
            foreach($list as $group){
                $content = array(
                    'title' => $group->getName()
                );
                $to = array();
                $members = array();
                $teams = $group->getTeams();
                foreach($teams as $team)
                {
                    $players = $team->getPlayers();
                    foreach($players as $player)
                    {
                        $to[] = $player->getEmail();
                    }
                    $members[$team->getId()]['name'] = $team->getName();
                    $members[$team->getId()]['image'] = $team->getImage();
                    
                    //$to[] = $this->getEmailFromTeamId($team->getId());
                }
                $content['members'] = $members;
                try{
                    $send = $notificationService->sendEmail($subject, $this->from, $to, null, "LiderBundle:Templates:notificationteam.html.twig", $content);
                    echo "Mensaje Enviado";
                    echo "\n $this->to";
                }catch(\Exception $e){
                    echo $e->getMessage();
                }
            }
                // $members[$key]['name'] = $value->getName();
            
            
        }
    }

    /**
     * Send Email when a team is created
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "adminNotification",
     *     description = "Send an Email to Admin when one player do something"
     * )
     */
    public function adminNotification(\GearmanJob $job){

        $notificationService = $this->co->get("notificationService");

        $data = json_decode($job->workload(),true);
        $admins = $this->getAdmins();
        echo "# admins: ".count($admins);
        $template = "LiderBundle:Templates:adminnotification.html.twig";
        if(array_key_exists("template", $data))
        {
            $template = $data['template'];
        }
        if($admins)
        {
            $to = array();
            $subject = $data['subject'];
            $body = $data['templateData'];
            foreach($admins as $value)
            {
                $to[] = $value->getEmail();
            }
            try{
                $send = $notificationService->sendEmail($subject, $this->from, $to, null, $template, $body);
                echo "Mensaje Enviado al administrador";
            }catch(\Exception $e){
                echo $e->getMessage();
            }
        }
    }

     /**
     * Send Email when a team is created
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "adminNotificationDuels",
     *     description = "Send an Email to Admin when one player do something"
     * )
     */
    public function adminNotificationDuels(\GearmanJob $job){

        $notificationService = $this->co->get("notificationService");

        $data = json_decode($job->workload(),true);
        $admins = $this->getAdmins();
        echo "# admins: ".count($admins);
        $template = "LiderBundle:Templates:duelsnotificationadmin.html.twig";
        if(array_key_exists("template", $data))
        {
            $template = $data['template'];
        }
        if($admins)
        {
            $to = array();
            $subject = $data['subject'];
            foreach($admins as $value)
            {
                $to[] = $value->getEmail();
            }
            $em = $this->co->get('doctrine')->getManager();
            $repo = $em->getRepository("LiderBundle:Game");
            $games = $repo->getGamesFromArrayId($data['content']['games']);
            $data['content']['games'] = $games;
            try{
                $send = $notificationService->sendEmail($subject, $this->from, $to, null, $template, $data['content']);
                echo "Mensaje Enviado al administrador";
            }catch(\Exception $e){
                echo $e->getMessage();
            }
        }
    }

    /**
     * Send Email games dont start
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(
     *     name = "adminNotificationGamesDontStart",
     *     description = "Send an Email to Admin from games dont start"
     * )
     */
    public function adminNotificationGamesDontStart(\GearmanJob $job)
    {
        $notificationService = $this->co->get("notificationService");

        $data = json_decode($job->workload(),true);
        $admins = $this->getAdmins();
        echo "# admins: ".count($admins);
        $template = "LiderBundle:Templates:duelsnotificationadmin.html.twig";
        if(array_key_exists("template", $data))
        {
            $template = $data['template'];
        }
        if($admins)
        {
            $to = array();
            $subject = $data['subject'];
            foreach($admins as $value)
            {
                $to[] = $value->getEmail();
            }
            $em = $this->co->get('doctrine')->getManager();
            $repo = $em->getRepository("LiderBundle:Game");
            $games = $repo->getGamesDontStart();
            $data['content']['games'] = $games;
            try{
                $send = $notificationService->sendEmail($subject, $this->from, $to, null, $template, $data['content']);
                echo "Mensaje Enviado al administrador";
            }catch(\Exception $e){
                echo $e->getMessage();
            }
        }
    }

    public function getAdmins()
    {
        $em = $this->co->get('doctrine')->getManager();
        $repo = $em->getRepository("LiderBundle:Player");
        $admins = $repo->findAdmin();
        return $admins;
    }
}
?>