<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Lider\Bundle\LiderBundle\Document\Image;

class ImageController extends SymfonyController
{
    /**
     * Obtener la imagen de mongo por id
     */
	public function getAction($id) {

		echo "entro aqui";
		$dm = $this->get('doctrine_mongodb')->getManager();
		$request = $this->get("request");
		$width = $request->get("width");
		$height = $request->get("height");
			
		$entity = $dm->getRepository("LiderBundle:Image")->findOneBy(array("id" => $id, "deleted" => false));
		if(!$entity)
			throw new \Exception("Entity no found");

		//Redimensionar imagen
		if($width && $height){
			$imagine = new \Imagine\Gd\Imagine();
			$image = $imagine->load($entity->getFile()->getBytes());
			$image->resize(new \Imagine\Image\Box($width, $height));			
	 		switch ($entity->getMimetype()) {
	            case 'image/gif' :
	                	$image->show('gif');
	                break;
	            case 'image/jpeg' :
	                	$image->show('jpg');
	                break;	                
	            case "image/png":
	                	$image->show('png');
	                break;
	            default             :
	                throw new InvalidArgumentException("Image type $type not supported");
	        }

			$response = new Response();
		}else{
			$response = new Response($entity->getFile()->getBytes());
		}

		
		//DISPOSITION_INLINE //DISPOSITION_ATTACHMENT
		//$d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, "png");
		//$response->headers->set('Content-Disposition', $d);
		//print_r($entity);
		$headers = array(
			"Content-Type" => $entity->getMimetype(),
			"filename" => $entity->getName()
		);
		$response->headers->add($headers);
		
		return $response;
		
	}


	/**
	 * Gurdar una imagen que no este asociada a una entidad
	 */
	public function saveImageAction(){

		$dm = $this->get('doctrine_mongodb')->getManager();
    	$request = $this->get("request");
    
    	$uploadedFile = $request->files->get('image');
    	$name = $request->get('name');
    	if($name == ""){
    		$name = $uploadedFile->getClientOriginalName();
    	}    	
		$image = new Image();
    	$image->setName($name);
    	$image->setFile($uploadedFile->getPathname());
    	$image->setMimetype($uploadedFile->getClientMimeType());    	
    
    	$dm->persist($image);
    	$dm->flush();
    	 
    	return $this->get("talker")->response(array("id" => $image->getId()));
	}


	// public function getAllImageAction(){

	// 	$dm = $this->get('doctrine_mongodb')->getManager();

	// 	$entity = $dm->getRepository("LiderBundle:Image")->findOneBy(array("deleted" => false, "entity" => null));
	// 	if(!$entity)
	// 		throw new \Exception("Entity no found");

	// 	//echo $entity->getName();
	// 	$arr = array();
	// 	foreach ($entity as $key => $value) {
			
	// 		$arr['imageId'] = $value->getId(),
	// 		$arr['name'] = $value->getName()
	// 		//"image" => $entity->getFile()->getBytes()
		
	// 	}
		
	// 	//print_r($arr);

	// 	return $this->get("talker")->response(array("images" => $arr));
	// }
}