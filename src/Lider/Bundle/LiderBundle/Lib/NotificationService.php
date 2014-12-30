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

    public function sendEmail($subject, $from, $to, $message = null, $viewName = null, array $viewparam = array()){
        $mail = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($from)
        // ->setTo($to)
        ->setBody($message);
        if(is_array($to)){
            $sw = false;
            foreach($to as $value){
                if(!$sw){
                    $mail->setTo($value);
                }
                else{
                    $mail->addCC($value);
                }
                $em = $this->registerEmailInLog($subject, $message, $from, $value);
                $sw = true;
            }
        }
        else{
            $mail->setTo($to);
            $em = $this->registerEmailInLog($subject, $message, $from, $to);
        }
        if(!is_null($viewName))
            $mail->addPart($this->templating->render($viewName, $viewparam), 'text/html');
        $mailer = $this->getNewConnection();
        $answer = $mailer->send($mail);
        $this->flushSpoolMailer();
        return $answer;
    }

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