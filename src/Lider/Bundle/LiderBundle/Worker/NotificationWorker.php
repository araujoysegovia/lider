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

    public function __construct($co){
        $this->co = $co;
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
            $send = $notificationService->sendEmail($data['subject'], $data['from'], $data['to'], null, $data['viewName'], array(
                "title" => $data['title'],
                "body" => $data['body']
            ));
            echo "Mensaje Enviado";
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        
    }
}
?>