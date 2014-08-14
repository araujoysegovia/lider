<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\ControllerController;
use Symfony\Component\HttpFoundation\Request;
use Lider\Bundle\LiderBundle\Document\Image;

class TeamController extends Controller
{
    public function getName(){
    	return "Team";
    }
    
    public function setImageAction($id) {
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository("LiderBundle:Team")->findOneBy(array("id" => $id, "deleted" => false));
    	if(!$entity)
    		throw new \Exception("Entity no found");
    	
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$request = $this->get("request");

    	$uploadedFile = $request->files->get('imagen');
    	$className = self::$NAMESPACE.$this->getName();
    	
    	$image = new Image();
    	$image->setName($uploadedFile->getClientOriginalName());
    	$image->setFile($uploadedFile->getPathname());
    	$image->setMimetype($uploadedFile->getClientMimeType());
		$image->setEntity($className);
		$image->setEntityId($id);
		    	
    	$dm->persist($image);
    	$dm->flush();
    	
    	$entity->setImage($image->getId());
    	
    	$em->flush();
    	
    	return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }
}
