<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Lider\Bundle\LiderBundle\Document\Email;

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
    
        $eState = $this->findSateByName("Sent");
    
    
        $email->setState($eState);
    
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

    public function sendEmail($subject, $from, $to, $message = null, $viewName = null, array $viewparam = array()){
        $em = $this->registerEmailInLog($subject, $message, $from, $to);
        $mail = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($from)
        ->setTo($to)
        ->setBody($message);
        if(!is_null($viewName))
            $mail->addPart($this->templating->render($viewName, $viewparam), 'text/html');

        $answer = $this->mailer->send($mail);
        $this->flushSpoolMailer();
        return $answer;
    }
    
    // public function sendEmail($subject, $message, $from, $to)
    // {
    //     $em = $this->registerEmailInLog($subject, $message, $from, $to);
    //     $mail = \Swift_Message::newInstance()
    //     ->setSubject($subject)
    //     ->setFrom($from)
    //     ->setTo($to)
    //     ->setBody($message);
    //     $mail->getHeaders()->addTextHeader('X-SMTPAPI', json_encode(array(
    //                 "unique_args" => array(
    //                         "logId" => $em->getId()
    //                  )
    //             )));
    //     $answer = $this->mailer->send($mail);
    //     $this->flushSpoolMailer();
    //     return $answer;
    // }

    private function flushSpoolMailer(){
        $spool = $this->mailer->getTransport()->getSpool();
        $transport = $this->co->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }
}
?>