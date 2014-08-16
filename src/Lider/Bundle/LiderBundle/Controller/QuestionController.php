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
    
//     public function updateAction($id = null) {
    
//     	//echo $id;
//     	$em = $this->getDoctrine()->getEntityManager();
    	
//     	$request = $this->get("request");
//     	$data = $request->getContent();
    	
//     	if(empty($data) || !$data)
//     		throw new \Exception("No data for update");
    		
//     	$data = json_decode($data, true);
    	
//     	$entityName = $this->getName();
//     	$bundleName = $this->getBundleName();
//     	$className = self::$NAMESPACE.$entityName;
    	
//     	if(!is_null($id)){
//     		$metadata = $em->getClassMetadata($className);
//     		$idField = $metadata->identifier[0];
//     		$data[$idField] = $id;
//     	}
    	
//     	echo "antes";
//     	$entity = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $data["id"], "deleted" => false));
//     	echo "desues";
//     	if(!$entity){
//     		throw new \Exception("Entity not found");
//     	}        	
//     	print_r($data);
// //     	if($data["question"]){
// //     		$
// //     	}
    	
//     	$this->beforeUpdate($ec);
//     	$doUpdate = $this->doUpdate($ec);
//     	$this->afterUpdate($doUpdate);
    	
    	
//     	return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
//     }    
}
