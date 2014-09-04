<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

class ImageController extends SymfonyController
{
    
	public function getAction($id) {
		
		$dm = $this->get('doctrine_mongodb')->getManager();
		$entity = $dm->getRepository("LiderBundle:Image")->findOneBy(array("id" => $id, "deleted" => false));
		if(!$entity)
			throw new \Exception("Entity no found");
		
		
		$response = new Response($entity->getFile()->getBytes());
		//DISPOSITION_INLINE //DISPOSITION_ATTACHMENT
		//$d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, "png");
		//$response->headers->set('Content-Disposition', $d);
		
		$headers = array(
				"Content-Type" => $entity->getMimetype(),
				"filename" => $entity->getName()
		);
		$response->headers->add($headers);
		
		return $response;
	}
}