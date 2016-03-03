<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Lider\Bundle\LiderBundle\Document\Email;
use Mailgun\Mailgun;


class NotificationService
{
	private $dm;
    private $co;
	private $mailer;
    private $templating;

	public function __construct($dm, $co, $mailer, $templating){
		$this->dm = $dm;
        $this->co = $co;
		$this->mailer = $mailer;
        $this->templating = $templating;
		$repo = $dm->getRepository('LiderBundle:EmailState');
        $this->emailStates = $repo->findAll();
	}
	
	public function registerEmailInLog($subject, $message, $from, $to)
    {
        $email = new Email();
        $email->setDatetime(new \MongoDate());
        $email->setSubject($subject);
        $email->setBody($message);
        $email->setFrom($from);
        $email->setTo($to);
        $email->setProviderId(null);
    
        // $eState = $this->findSateByName("Sent");
    
    
        // $email->setState($eState);
    
        $this->dm->persist($email);
        $this->dm->flush();
    
        return $email;
    }
    
    private function findSateByName($name){
        $state = null;
        foreach ($this->emailStates as $entity) {
            if($name == $entity->getName()){
                $state = $entity;
            }
        }
        return $state;
    }

    public function sendEmail($subject, $from, $to, $message = null, $viewName = null, array $viewparam = array(),$viewString = null,$entityId = null, $entityRepository = null,$typeRepository = null, $registerLog = true, $emailId = null, $dateToSend = null, array $attachment = null, $referenceOne = null, $referenceTwo = null){
        $html = "";
        if(!is_null($viewString)){
            $html = $this->getHtml($viewString, $viewparam);
        }

        if(!is_null($viewName)){
            $html = $this->templating->render($viewName, $viewparam);
        }
        if($message){

        }else{
            $message = $html;
        }

        $mail = array(
            'from' => $from,
            'subject' => $subject,
            'html' => $message
        );

        if(is_array($to)){
            $sw = false;
            foreach($to as $value){
                if(!$sw){
                    $mail['to'] = $value;
                }
                else{
                    $mail['cc'][] = $value;
                }
                $sw = true;
            }
        }
        else{
            $mail['to'] = $to;
        }

        $mgClient = new Mailgun('key-8e3rd475inaidd-6x6laxkyw6rl8mn51');
        $domain = "ays.mailgun.org";
        $result = $mgClient->sendMessage($domain, $mail);
        # Make the call to the client.
        if($registerLog)
        {
            if($result->http_response_code == 200)
            {
                $providerId = $result->http_response_body->id;
                $providerId = str_replace("<", "", $providerId);
                $providerId = str_replace(">", "", $providerId);
                if(is_array($to)){
                    foreach($to as $value){
                        $em = $this->registerEmailInLog($subject, $message, $from, $value);
                    }
                }
                else{
                    $em = $this->registerEmailInLog($subject, $message, $from, $to);
                }
            }
            return $result;
        }
    }

    private function getHtml($templateString,$data){
        //$node = $this->co->get('node_service');
        $html = $this->co->get('mine.twig_string')->render(
            $templateString,
            $data
        );

        return $html;
    }


    // public function sendEmail($subject, $from, $to, $message = null, $viewName = null, array $viewparam = array()){
    //     $mail = \Swift_Message::newInstance()
    //     ->setSubject($subject)
    //     ->setFrom($from)
    //     // ->setTo($to)
    //     ->setBody($message);
    //     if(is_array($to)){
    //         $sw = false;
    //         foreach($to as $value){
    //             if(!$sw){
    //                 $mail->setTo($value);
    //             }
    //             else{
    //                 $mail->addCC($value);
    //             }
    //             $em = $this->registerEmailInLog($subject, $message, $from, $value);
    //             $sw = true;
    //         }
    //     }
    //     else{
    //         $mail->setTo($to);
    //         $em = $this->registerEmailInLog($subject, $message, $from, $to);
    //     }
    //     if(!is_null($viewName))
    //         $mail->addPart($this->templating->render($viewName, $viewparam), 'text/html');
    //     $mailer = $this->getNewConnection();
    //     //$answer = $mailer->send($mail);
    //     $this->flushSpoolMailer();
    //     return $mail;
    // }

    private function getNewConnection()
    {
        $transport = \Swift_SmtpTransport::newInstance($this->co->getParameter('mailer_host'), 465, 'ssl');
        $transport->setUsername($this->co->getParameter('mailer_user'));
        $transport->setPassword($this->co->getParameter('mailer_password'));
        $mailer = \Swift_Mailer::newInstance($transport);
        return $mailer;
    }

    private function flushSpoolMailer(){
        $spool = $this->mailer->getTransport()->getSpool();
        $transport = $this->co->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }
}
?>