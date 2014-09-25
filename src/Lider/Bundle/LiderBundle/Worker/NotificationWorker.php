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
        $notificationService = $this->co->get("notificationService");
        try{
            $send = $notificationService->sendEmail($data['subject'], $data['from'], $data['to'], null, $data['viewName'], $data['content']);
            echo "Mensaje Enviado";
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
     *     name = "notificationTeam",
     *     description = "Send an Email when need a notification for the members of the team"
     * )
     */
    public function sendNotificationEmailCreate(\GearmanJob $job){
        $data = json_decode($job->workload(),true);        
        $team = $data['team'];        
        $em = $this->co->get('doctrine')->getManager();
        $notificationService = $this->co->get("notificationService");
        $repo = $em->getRepository("LiderBundle:Player");
        $list = $repo->findBy(array("team" => $team->getId(), "deleted" => FALSE));
        $subject = "Este es tu Equipo!!!";
        $to = array();
        $content = array(
            "teamImage" => $team->getImage(),
            "title" => $team->getName(),
        );
        $members = array();
        if($list){
            foreach($list->toArray() as $key => $value){
                $to[] = $value->getId();
                $members[$key]['image'] = $value->getImage();
                $members[$key]['name'] = $value->getName();
                $members[$key]['lastname'] = $value->getLastname();
            }
            $content['members'] = $members;
            try{
                $send = $notificationService->sendEmail($subject, $this->from, $to, null, "LiderBundle:Templates:notificationteam.html.twig", $content);
                echo "Mensaje Enviado";
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
     *     name = "adminNotification",
     *     description = "Send an Email to Admin when something happen"
     * )
     */
    public function adminNotification(\GearmanJob $job){
        $data = json_decode($job->workload(),true);
        $em = $this->co->get('doctrine')->getManager();
        $notificationService = $this->co->get("notificationService");
        $repo = $em->getRepository("LiderBundle:Player");
        $admins = $repo->findAdmin();
        if($admins){
            $to = array();
            $subject = $data['subject'];
            $body = $data['templateData'];
            foreach($admins as $value)
            {
                $to[] = $value->getEmail();
            }
            try{
                $send = $notificationService->sendEmail($subject, $this->from, $to, null, "LiderBundle:Templates:adminnotification.html.twig", $body);
                echo "Mensaje Enviado";
            }catch(\Exception $e){
                echo $e->getMessage();
            }
        }
        
    }
}
?>