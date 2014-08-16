<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\ControllerController;
use Symfony\Component\HttpFoundation\Request;

class QuestionController extends Controller
{
    public function getName(){
    	return "Question";
    }
    
    public function checkAction($id){
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $id, "deleted" => false));
    	if(!$entity)
    		throw new \Exception("Entity no found");
    	
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$entity->setUser($user);
    	$entity->setChecked(true);
    	
    	$em->flush();
    	
    	return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    	
    }
}
