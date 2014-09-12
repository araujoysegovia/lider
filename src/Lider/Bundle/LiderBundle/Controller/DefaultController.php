<?php

namespace Lider\Bundle\LiderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Lider\Bundle\LiderBundle\Entity\Role;
use Lider\Bundle\LiderBundle\Entity\Player;
use Lider\Bundle\LiderBundle\Document\EmailState;

class DefaultController extends SymfonyController
{
    public function indexAction($name)
    {
        return $this->render('LiderBundle:Default:index.html.twig', array('name' => $name));
    }
    
	public function createDefaultDataAction(){
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$role = $this->createDefaultRole($em);
    	$this->createDefaultUser($em, $role);
    	$em->flush();
    	return new Response("Default data is created");
    }
    
    public function getHomeAction()
    {
         return $this->render('LiderBundle:Default:home.html.twig');
    }    

    private function createDefaultUser($em, $role){
    	$user = new Player();
    	$user->setEmail("dmejia@araujoysegovia.com");
    	$user->setName("Deiner");
    	$user->setLastname("Mejia");
    	$user->addRole($role);
    	
    	$factory = $this->container->get('security.encoder_factory');
    	$codificador = $factory->getEncoder($user);
    	$password = $codificador->encodePassword("araujo123", $user->getSalt());
    	
    	$user->setPassword($password);
    	
    	$em->persist($user);
    	
    	$user1 = new Player();
    	$user1->setEmail("lrodriguez@araujoysegovia.com");
    	$user1->setName("Lizeth");
    	$user1->setLastname("Rodriguez");
    	$user1->addRole($role);
    	
    	$factory = $this->container->get('security.encoder_factory');
    	$codificador = $factory->getEncoder($user1);
    	$password = $codificador->encodePassword("araujo123", $user1->getSalt());
    	 
    	$user1->setPassword($password);

    	$em->persist($user1);




    }

    public function MongoDataAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $emailStates = $dm->getRepository('LiderBundle:EmailState');
        $email = $emailStates->findAll();
        foreach($email as $em)
        {
            $dm->remove($em);
        }
        try
        {
            $dm->flush();
        }
        catch(\Exception $e)
        {
        }
        $emailStateArray = array(
            array("Name" => "Sent", "Description" => "Email Enviado"),
            array("Name" => "Processed", "Description" => "Email Procesado"),
            array("Name" => "Dropped", "Description" => "Email Droppeado"),
            array("Name" => "Delivered", "Description" => "Email Rechazado"),
            array("Name" => "Deferred", "Description" => "Email Diferido"),
            array("Name" => "Bounce", "Description" => "Email Rebotado"),
            array("Name" => "Open", "Description" => "Email Abierto"),
            array("Name" => "Click", "Description" => "Email Clickeado"),
            array("Name" => "Spam Report", "Description" => "Email Reportado como Spam"),
            array("Name" => "Unsubscribe", "Description" => "Email Desinscrito"),
        );
        foreach($emailStateArray as $es)
        {
            $emailState = new EmailState();
            $emailState->setDatetime(new \MongoDate());
            $emailState->setName($es['Name']);
            $emailState->setDescription($es['Description']);
            $dm->persist($emailState);
        }
        try
        {
            $dm->flush();
        }
        catch(\Exception $e)
        {
        }
        return new Response("Informacion Guardada");
    }

    public function sendEmailAction(){
        $gearman = $this->get("gearman");
        $em = $this->getDoctrine()->getEntityManager();
        $notificationService = $this->get("notificationService");

        $subject = "Subject";
        $subject2 = "Subject desde gearman";
        $message = "Mensaje a enviar";
        $message2 = "Mensaje a enviar desde gearman";
        $title = "Titulo del Email";
        $body = "CUerpo del mensaje";
        $from = "eescallon@araujoysegovia.com";
        $to = "eduard.escallon@gmail.com";
        $viewName = "LiderBundle:Templates:email.html.twig";
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', json_encode(array(
            "subject" => $subject2,
            "message" => $message2,
            "from" => $from,
            "to" => $to,
            "viewName" => $viewName,
            "title" => $title,
            "body" => $body
        )));
        // $returnCode = $gearman->getReturnCode();
        // $send = $notificationService->sendEmail($subject, $message, $from, $to);
        return new Response("Email Enviado");
    }
    
    
    private function createDefaultRole($em){   	
    	$adminrole = new Role();    	
    	$adminrole->setName("ADMIN");
    	$adminrole->setDescription("Administrator role");
    	$em->persist($adminrole);    	
    	
    	$userrole = new Role();
    	$userrole->setName("USER");
    	$userrole->setDescription("Usuario role");
    	$em->persist($userrole);
    	
    	return $adminrole;
    }

    public function testSaveAction()
    {
    	return $this->render("LiderBundle:Administrator:playerImage.html.twig");
    }
}
