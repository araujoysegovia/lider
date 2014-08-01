<?php

namespace Lider\Bundle\LiderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Lider\Bundle\LiderBundle\Entity\Role;
use Lider\Bundle\LiderBundle\Entity\Player;

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
    
    
    private function createDefaultRole($em){    	
    	$adminrole = new Role();    	
    	$adminrole->setName("ROLE_ADMIN");
    	$adminrole->setDescription("Administrator role");
    	$em->persist($adminrole);    	
    	
    	$userrole = new Role();
    	$userrole->setName("ROLE_USER");
    	$userrole->setDescription("Usuario role");
    	$em->persist($userrole);
    	
    	return $adminrole;
    }
    
}
