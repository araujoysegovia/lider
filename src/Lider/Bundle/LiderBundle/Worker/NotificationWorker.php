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
	        echo "\n\n\n\nVoy a consultar ".$data['to'];
	        $player = $this->co->get('doctrine')->getManager()->getRepository("LiderBundle:Player")->findOneByEmail($data['to']);
	        if(!$player)
	        	return;
	        
	        echo "\n\nVoy a enviar correo a ".$player->getName()." ".$player->getName();
	        $team = $player->getTeam();
//         $to = $this->getEmailFromTeamId($team->getId());
       

            //$send = $notificationService->sendEmail($data['subject'], $this->from, $data['to'], null, $data['viewName'], $data['content']);
            echo "\n\nMensaje Enviado a ".$data['to'].":";
            //print_r($data['content']);

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
        $notificationService = $this->co->get("notificationService");
        $repoDuel = $em->getRepository("LiderBundle:Duel");
        $list = $repoDuel->findBy(array("tournament" => $tournamentId, "deleted" => FALSE, "active" => true, "finished" => false));
        $subject = "Duelo generado!!!";
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
                $send = $notificationService->sendEmail($subject, $this->from, $to, null, "LiderBundle:Templates:adminnotification.html.twig", $body);
                echo "Mensaje Enviado al administrador";
            }catch(\Exception $e){
                echo $e->getMessage();
            }
        }
    }

    private function getAdmins()
    {
        $em = $this->co->get('doctrine')->getManager();
        $repo = $em->getRepository("LiderBundle:Player");
        $admins = $repo->findAdmin();
        return $admins;
    }
}
?>