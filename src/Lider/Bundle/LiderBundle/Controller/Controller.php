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
	
	public function getAnswer($success, $message) {
		return array (
				"success" => $success,
				"message" => $message
		);
	}
	
	private function setPropertyChanges($propertyName, $oldValue, $newValue, $entityId){
		$this->propertyChanged[] = array(
			"propertyName" => $propertyName,
			"oldValue" => $oldValue,
			"newValue" => $newValue,
			"entityId" => $entityId
		);
	}
	
	protected function getRequestEntity($id = null){
		
		
		$request = $this->get("request");
		$contentType = $request->headers->get('content_type');
		$explode = explode(";", $contentType);
		$contentType = $explode[0];
		
		switch ($contentType) {
			case 'application/x-www-form-urlencoded':
				return $this->applicationForm();
				break;
			case 'multipart/form-data':
				return $this->applicationForm();
				break;
					
			default:
				return $this->applicationJson($id);
				break;
		}
	}
	
	private function applicationForm(){
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->get("request");
		$entityName = $this->getName();
		$bundleName = $this->getBundleName();
		$className = self::$NAMESPACE.$entityName;
		
		$reflectionClass = new \ReflectionClass($className);
		$metadata = $em->getClassMetadata($className);
		$associations = $metadata->associationMappings;
		$fieldMapping = $metadata->fieldMappings;
		
		do {
			$props = $reflectionClass->getProperties();
			foreach ($props as $prop) {
				$value = $request->get($prop->getName());
				$setter = "set" . ucwords($prop->getName());
				if(!$is_null($value)){
					if (array_key_exists($prop->getName(), $associations)) {
						$asso = $associations[$prop->getName()];
						$entity = $asso["targetEntity"];
						$obj = $em->getRepository($entity)->find($value);
						$value = $obj;
						if($asso["type"] == 4 || $asso["type"] == 8){
							if (substr($prop->getName(), -1) == "s")
								$setter = 'add' . ucwords(substr($prop->getName(), 0, -1));
							else
								$setter = 'add' . ucwords($prop->getName());
						}
					}elseif (array_key_exists($prop->getName(), $fieldMapping)) {
						if ($fieldMapping[$prop->getName()]["type"] == "datetime") {
							$date = $value;
							$value = new \DateTime($date);
						}
					}
					if(method_exists($newClass, $setter))
						$newClass->$setter($value);
				}
			}
			$reflectionClass = $reflectionClass->getParentClass();
		} while (false !== $reflectionClass);
		
		return $newClass;
	}
	
	private function applicationJson($id = null){
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->get("request");
		$data = $request->getContent();
		
		if(empty($data) || !$data)
			throw new \Exception("No data for update");
			
		$data = json_decode($data, true);
		
		$entityName = $this->getName();
		$bundleName = $this->getBundleName();
		$className = self::$NAMESPACE.$entityName;
				
		if(!is_null($id)){
			$metadata = $em->getClassMetadata($className);			
			$idField = $metadata->identifier[0];
			$data[$idField] = $id;
		}
		
		
		$newClass = $this->get("talker")->denormalizeEntity($className, $data);
		
		return $newClass;
	}
	
	public function listAction($id = null) {
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->get("request");
		if(is_null($id)){
			$bundleName = $this->getBundleName();
			$className = self::$NAMESPACE.$this->getName();
			$md = $em->getClassMetadata($className);
			$associations = $md->associationMappings;
			$fieldMapping = $md->fieldMappings;
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
			
			$user = $this->container->get('security.context')->getToken()->getUser();

			$roles = $user->getRoles();
			$role = $roles[0];
			
			if($role->getId() > 1 && array_key_exists("company", $associations)){
				$criteria["company"] = $user->getCompany()->getId();
			}
						
			$filter = $request->get('filter');
			if($filter){
				$filter = json_decode($filter, true);
			}
			
			$page = $request->get("page");
			$start = $request->get("start");
			$limit = $request->get("limit");			
	
			$bundleName = $this->getBundleName();
			$repo = $em->getRepository($bundleName.":" . $this->getName());
			$list = $repo->getArrayEntityWithOneLevel($criteria, "id", $start, $limit, $filter);
			$this->afterList($list);
			
			return $this->get("talker")->response($list);
			
		}else{
			$bundleName = $this->getBundleName();
			$repo = $em->getRepository($bundleName.":" . $this->getName());
			$list = $repo->getArrayEntityWithOneLevel(array("id" => $id));
			$list = $list["data"];
			if(count($list)>0) $list = $list[0];
			$this->afterList($list);
			return $this->get("talker")->response($list);
		}
		
		
	}
	
	protected function getBundleName(){
		$request = $this->get("request");
		$controller = $request->attributes->get('_controller');		
		$matches    = explode("\\", $controller);		
		if(count($matches) > 1)
			return  $matches[2];
		else{
			$matches = explode(":", $controller);
			return  $matches[0];
		}
	}
	
	public function checkRouteAction(Request $request)
	{
		$method = $request->getMethod();
		$comment = $request->get('comment');
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
	
	protected function doSave(&$entity) {
		$this->beforeSave($entity);
		$em = $this->getDoctrine()->getEntityManager();
		if(is_array($entity)){
			foreach($entity as $ent){
				$em->persist($ent);
			}
		}else{
			$em->persist($entity);
		}
		
		$this->afterPersist($entity);
		
		$validator = $this->get("validator");
		$errors = $validator->validate($entity);
		if (count($errors) > 0) {
			throw new RuntimeException($errors->__toString());
		}
		
		$em->flush();
		$this->afterSave($entity);
		return $entity;
	}
	
	public function saveAction() {
		$entity = $this->getRequestEntity();
		$save = $this->doSave($entity);
		return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
	}
	
	// +++++++++++++++++++ UPDATE +++++++++++++++++++++++++++++
	
	protected function doUpdate(&$entity) {
		$em = $this->getDoctrine()->getEntityManager();
		$validator = $this->get("validator");
		$errors = $validator->validate($entity);
		if (count($errors) > 0) {
			throw new RuntimeException($errors->__toString());
		}
		$em->flush();
		//echo $entity;
		return $entity;
	}
	
	public function updateAction($id = null) {
		
		$ec = $this->getRequestEntity($id);		
		if(is_null($ec->getId())){
			throw new \Exception("Entity not found");
		}
		$this->beforeUpdate($ec);
		$doUpdate = $this->doUpdate($ec);
		$this->afterUpdate($doUpdate);
		return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
	}
	
	
	// +++++++++++++++++++ DELETE +++++++++++++++++++++++++++++
	
	protected function doDelete($entity) {
		$em = $this->getDoctrine()->getEntityManager();
		if(is_array($entity)){
			foreach($entity as $ent){
				$ent->setDeleted(true);
			}
		}else{
			$entity->setDeleted(true);
		}
		$em->flush();
	}
	
	public function deleteAction($id = null) {
		if(!is_null($id)){
			$bundleName = $this->getBundleName();
			$em = $this->getDoctrine()->getEntityManager();
			$repo = $em->getRepository($bundleName.":" . $this->getName());
			$ec = $repo->find($id);
		}else{
			$ec = $this->getRequestEntity();
		}
		$this->beforeDelete($ec);
		$this->entityDeleted = $ec;
		$doDelete = $this->doDelete($ec);
		//throw new \Exception("dntrcd");
		$this->afterDelete($doDelete);
		return $this->get("talker")->response($this->getAnswer(true, $this->delete_successful));
	}
	
	
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
	
	
	protected function applyPagination(&$data, $count, $start, $limit) {
		$paginateData = array ();
		for($i = $start; $i < ($start + $limit); $i ++) {
			if ($count > $i) {
				$paginateData [] = $data [$i];
			}
		}
		$data = $paginateData;
		return $data;
	}
	
	
	abstract protected function getName();
	
	
	protected function addCompany(&$Entity, $addServer = true){
		$user = $this->container->get('security.context')->getToken()->getUser();
		$roles = $user->getRoles();
		$role = $roles[0];
		
		$em = $this->getDoctrine()->getEntityManager();
		$repo = $em->getRepository("AdminBundle:Company");
		$company = $repo->find($user->getCompany()->getId());
		
		if($role->getId() > 1){
			$Entity->setCompany($company);
			if(!$company->getServer()){
				throw new \Exception("Company does not have an associated server");
			}
			if($addServer && method_exists($Entity, "setServer"))
				$Entity->setServer($company->getServer());
			
			return $user->getCompany();
		}else{ 
			if($addServer && method_exists($Entity, "setServer")){
				$company = $repo->find($Entity->getCompany()->getId());
				if(!$company->getServer()){
					throw new \Exception("Company does not have an associated server");
				}
        		$Entity->setServer($company->getServer());
        		
			}
        }
	}
	
	protected function generatePassword($l) {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = "";
		for($i = 0; $i < $l; $i ++) {
			$n = rand ( 0, strlen ( $alphabet ) - 1 );
			$pass .= $alphabet [$n];
		}
		return $pass;
	}
	
}