<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;


/**
* Controller
*
* Esta clase contiene atributos y funciones generales eferentes a Entidades
*
* @package    Dialboxes
* @subpackage AppSecurityBundle
* @author     Ing. Deiner Mejia M. < deiner37@gmail.com >
*/
abstract class Controller extends SymfonyController {
	
	public static $NAMESPACE = "Lider\Bundle\LiderBundle\Entity\\";
	
	protected $save_successful = "Save Successful";
	
	protected $update_successful = "Update Successful";
	
	protected $delete_successful ="Delete Successful";
	
	private $propertyChanged = array();
	
	public $documentNameSpace = "Lider\Bundle\LiderBundle\Document\\";

	public function getAnswer($success, $message) {
		return array (
			"success" => $success,
			"message" => $message
		);
	}
	
	public function listAction($id = null) {
		
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->get("request");
		$className = self::$NAMESPACE.$this->getName();
		$md = $em->getClassMetadata($className);
		$associations = $md->associationMappings;
		$fieldMapping = $md->fieldMappings;
		if(is_null($id)){
			
			$criteria = array();
			foreach($associations as $name => $value){
				$parameter = $request->get($name);
				if($parameter)
				{
					$criteria[$name] = $parameter;
				}
			}
		
			foreach($fieldMapping as $name => $value){
				$parameter = $request->get($name);
				if($parameter)
				{
					$criteria[$name] = $parameter;
				}
			}
			
			$user = $this->get('security.context')->getToken()->getUser();

			$roles = $user->getRoles();
			$role = $roles[0];
			if($role->getId() > 1 && array_key_exists("company", $associations)){
				$criteria["company"] = $user->getCompany()->getId();
			}
			$filter = $request->get('filter');
			
			if($filter == "")
			{
				$filter = null;
			}
			if($filter){
				$filter = json_decode($filter, true);
			}
			
			$page = $request->get("page");
			$start = $request->get("skip");
			$limit = $request->get("pageSize");	
			$sort = $request->get("sort");
			$sortField = null;
			$sortType = null;
			if(!is_null($sort))
			{
				$sortField = $sort[0]['field'];
				$sortType = $sort[0]['dir'];
			}
	
			$bundleName = $this->getBundleName();			
			$repo = $em->getRepository($bundleName.":" . $this->getName());
			$list = $repo->getArrayEntityWithOneLevel($criteria, $sortField, $start, $limit, $filter, $sortType);
			$this->afterList($list);
			
			return $this->get("talker")->response($list);
			
		}else{
			$idProperty = $md->identifier;
			$idProperty = $idProperty[0];
			$idType = $fieldMapping[$idProperty]['type'];
			$val = $this->validateType($idType, $id);
			if($val)
			{
				$bundleName = $this->getBundleName();
				$repo = $em->getRepository($bundleName.":" . $this->getName());
				$list = $repo->getArrayEntityWithOneLevel(array("id" => $id));
				$list = $list["data"];
				if(count($list)>0) $list = $list[0];
				else throw $this->createNotFoundException('El recurso no existe.');
				$this->afterList($list);
				return $this->get("talker")->response($list);
			}
			else{
				throw $this->createNotFoundException('El recurso no existe.');
			}
			
		}
	}

	protected function validateType($type, $value)
	{
		switch ($type) {
			case 'uuid':
				if(preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', $value, $matches)){
					return true;
				}
				else{
					return false;
				}
				break;
			case 'integer':
				if(is_numeric($value))
				{
					return true;
				}
				else{
					return false;
				}
			
			default:
				# code...
				break;
		}
		
	}
	
	protected function getBundleName(){
		$request = $this->get("request");
		$controller = $request->attributes->get('_controller');		
		$matches    = explode("\\", $controller);
		if(count($matches) > 1){			
			return  $matches[2];
		}else{			
			$matches = explode(":", $controller);
			return  $matches[0];
		}
	}

	protected function normalizer($entity){        
        $em = $this->get('doctrine_mongodb')->getManager();
        $md = $em->getClassMetadata($document);
                
        $fm = $md->fieldMappings;
        $arr = array();
        foreach($fm as $key => $value)
        {
            $arr[$key] = $entity->{'get'.ucfirst($key)}();
        }
        return $arr;
    }
    
	/**
	 * Function que retorna los comentarios de una entidad
	 *
	 * @param id - Id de la entidad
	 * 
	 * @return array - Array con los comentarios
	 */
	public function getComments($uuid){
		$dm = $this->get('doctrine_mongodb')->getManager();
		$commentService = $this->get("commentService");
		if(!$uuid)
		{
			throw new Exception("Id de la entidad necesario");
		}
		$repo = $dm->getRepository("CommentBundle:Comment");
		$comments = $commentService->getCommentTree($this->namespace.$this->getName(), $uuid);
		$ret=array();
        foreach($comments as $id => $obj){
            $entity = $this->normalizer($obj, "Sifinca\\CommentBundle\\Document\\Comment");
            $ret[$id] = $entity;
            if($obj->getChildren()){
            	$list = $repo->consultCommentsFromParent($this->namespace.$this->getName(), $uuid, $id);
            	$child = array();
            	foreach($list as $key => $value){
            		// echo "fjasdlñ";
            		$ent = $this->normalizer($value, "Sifinca\\CommentBundle\\Document\\Comment");
            		$child[] = $ent;
            	}
            	$ret[$id]['children'] = $child;
            }
            
        }
        return $this->get("talker")->response(array("total" => count($ret), "data" => $ret));
	}

	public function getParticipantsAction($id){
		$commentService = $this->get("commentService");
		if(!$id)
		{
			throw new Exception("Id de la entidad necesario");
		}
		$participants = $commentService->getParticipantFromEntity($this->namespace.$this->getName(), $id);
		$ret=array();
        foreach($participants as $id => $obj){
            $entity = $this->normalizer($obj, "Sifinca\\CommentBundle\\Document\\Participant");
            $ret[] = $entity;
        }
        return $this->get("talker")->response(array("total" => count($ret), "data" => $ret));
	}

	public function getCommentsFromParticipantsAction($idEntity, $idParticipant){
		if(!$idEntity || $idParticipant)
		{
			throw new Exception("Id de la entidad o del participante necesario");
		}
		$commentService = $this->get("commentService");
		$comments = $commentService->getCommentsFromParticipant($this->namespace.$this->getName(), $idEntity, $idParticipant);
		$ret=array();
        foreach($comments as $id => $obj){
            $entity = $this->normalizer($obj, "Sifinca\\CommentBundle\\Document\\Comment");
            $ret[] = $entity;
        }
        return $this->get("talker")->response(array("total" => count($ret), "data" => $ret));
	}


	
	public function checkRouteAction(Request $request)
	{
		$method = $request->getMethod();
		switch($method)
		{
			case 'GET':		
				return $this->listAction();
				break;
			case 'POST':				
				return $this->saveAction();
				break;
			case 'PUT':
				return $this->updateAction();
				break;
			case "DELETE":
				return $this->deleteAction();
				break;
		}
	}
	
	// +++++++++++++++++++ SAVE +++++++++++++++++++++++++++++
	public function saveAction() {
		$entityService = $this->get("entityController");
		$entityName = $this->getName();
		$className = self::$NAMESPACE.$entityName;		
		$entity = $entityService->getRequestEntity($className);
		$this->beforeSave($entity);
		$save = $entityService->doSave($entity);
		$this->afterSave($entity);
		$ids = array();
		if(is_array($entity))
		{
			foreach($entity as $ent)
			{
				//$this->addParticipant($ent);
				$ids[] = $ent->getId();
			}
		}
		else{
			//$this->addParticipant($entity);
			$ids[] = $entity->getId();
		}
		return $this->get("talker")->response(array("success" => true, "message" => $this->save_successful, "total" => count($ids), "data" => $ids), 201);
	}
	
	// +++++++++++++++++++ UPDATE +++++++++++++++++++++++++++++
	public function updateAction($id = null) {
		$em = $this->getDoctrine()->getEntityManager();
		$entityName = $this->getName();
		$className = self::$NAMESPACE.$entityName;
		$entityService = $this->get("entityController");
		$md = $em->getClassMetadata($className);
		$associations = $md->associationMappings;
		$fieldMapping = $md->fieldMappings;
		$idProperty = $md->identifier;
		$idProperty = $idProperty[0];
		$idType = $fieldMapping[$idProperty]['type'];
		$val = $this->validateType($idType, $id);
		if($val)
		{
		
			$ec = $entityService->getRequestEntity($className, $id);
			
		}
		else{
			throw $this->createNotFoundException('Entity Not Found.');
		}
		$this->beforeUpdate($ec);
		$doUpdate = $entityService->doUpdate($ec);
		$this->afterUpdate($doUpdate);
		return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
	}
	
	
	// +++++++++++++++++++ DELETE +++++++++++++++++++++++++++++
	public function deleteAction($id = null) {
		$entityService = $this->get("entityController");
		$entityName = $this->getName();
		$className = self::$NAMESPACE.$entityName;
		if(!is_null($id)){
			$bundleName = $this->getBundleName();
			$em = $this->getDoctrine()->getEntityManager();
			$repo = $em->getRepository($bundleName.":" . $this->getName());
			$md = $em->getClassMetadata($className);
			$associations = $md->associationMappings;
			$fieldMapping = $md->fieldMappings;
			$idProperty = $md->identifier;
			$idProperty = $idProperty[0];
			$idType = $fieldMapping[$idProperty]['type'];
			$val = $this->validateType($idType, $id);
			if($val){
				$ec = $repo->findOneBy(array("id" => $id, "deleted" => false));
				if(!$ec)
				{
					throw $this->createNotFoundException('Entity Not Found.');
				}
			}
			else
				throw $this->createNotFoundException('Entity Not Found.');
		}else{

			$ec = $entityService->getRequestEntity($className);
		}
		$this->beforeDelete($ec);
		$this->entityDeleted = $ec;
		$doDelete = $entityService->doDelete($ec);
		$this->afterDelete($doDelete);
		return $this->get("talker")->response($this->getAnswer(true, $this->delete_successful), 204);
	}

	// public function addParticipant($entity)
	// {
	// 	$user = $this->get('security.context')->getToken()->getUser();
	// 	$commentService = $this->get("commentService");
	// 	if($user->getId())
	// 	{
	// 		$namespace = $this->namespace.$this->getname();
	// 		$commentService->addParticipant($namespace, $entity->getId(), $user->getId(), true);	
	// 	}
	// }
	
	/**
	 * Funcin que realiza alguna accion despues de listar una entidad.
	 */
	protected function afterList(&$Entity) {
	}
	
	/**
	 * Funcin que realiza alguna accion antes de guardar una entidad.
	 */
	protected function beforeSave(&$Entity) {
	}
	
	/**
	 * Funcin que realiza alguna accion despues de guardar una entidad.
	 */
	protected function afterSave(&$Entity) {
	}
	
	protected function afterPersist(&$Entity) {
	}
	
	/**
	 * Funcin que realiza alguna accion antes de actualizar una entidad.
	 */
	protected function beforeUpdate(&$Entity) {
	}
	
	/**
	 * Funcin que realiza alguna accion despues de actualizar una entidad.
	 */
	protected function afterUpdate(&$Entity) {
	}
	
	/**
	 * Funcin que realiza alguna accion antes de eliminar una entidad.
	 */
	protected function beforeDelete(&$Entity) {
	}
	
	/**
	 * Funcin que realiza alguna accion despues de eliminar una entidad.
	 */
	protected function afterDelete(&$Entity) {}
	
	abstract protected function getName();

}