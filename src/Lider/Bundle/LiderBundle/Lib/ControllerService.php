<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ControllerService
{
	private $propertyChanged = array();

    private $em;

    private $container;
    
    private $myBundles = array(
        "LiderBundle"        
    );
    // protected $nameSpace = "AYS\ArchiveBundle\Entity\\";

    public function __construct($em, $co) {
        $this->em = $em;
        $this->container = $co;
        
    }

    /**
     * Funcion para colocar entre comillas el valor de una variable booleana
     */
    public function checkBoolean(&$Entity, $className)
    {
        $md = $this->em->getClassMetadata($className);
        $associations = $md->associationMappings;
        $fieldMapping = $md->fieldMappings;
        foreach($fieldMapping as $key => $value)
        {
            if($value['type'] == "boolean")
            {
                // echo $key;
                $val = $Entity->{'get'. ucfirst($key)}();
                if($val == true)
                {
                    // echo "hola1";
                    $Entity->{'set'. ucfirst($key)}("true");
                }
                else
                {
                    // echo "hola2";
                    $Entity->{'set'. ucfirst($key)}("false");
                }
            }
        }
    }
	
	public function setPropertyChanges($propertyName, $oldValue, $newValue, $entityId){
		$this->propertyChanged[] = array(
			"propertyName" => $propertyName,
			"oldValue" => $oldValue,
			"newValue" => $newValue,
			"entityId" => $entityId
		);
	}
	
	public function getRequestEntity($className, $id = null){
		$request = $this->container->get("request");
		$contentType = $request->headers->get('content_type');
		// print_r($contentType);
		$explode = explode(";", $contentType);
		$contentType = $explode[0];		
		switch ($contentType) {
			case 'application/x-www-form-urlencoded':
				return $this->applicationForm($className);
				break;
			case 'multipart/form-data':
				return $this->applicationForm($className);
				break;
					
			default:
				return $this->applicationJson($className, $id);
				break;
		}
	}
	
	private function applicationForm($className){
		$request = $this->container->get("request");
		$reflectionClass = new \ReflectionClass($className);
		$metadata = $this->em->getClassMetadata($className);
		$associations = $metadata->associationMappings;
		$fieldMapping = $metadata->fieldMappings;
		$newClass = new $className();
		do {
			$props = $reflectionClass->getProperties();
			foreach ($props as $prop) {
				// echo $prop->getName();
				

				$value = $request->get($prop->getName());
				$setter = "set" . ucwords($prop->getName());
				if(!is_null($value)){
					if (array_key_exists($prop->getName(), $associations)) {
						$asso = $associations[$prop->getName()];
						$entity = $asso["targetEntity"];
						$obj = $this->em->getRepository($entity)->find($value);
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
	
	private function applicationJson($className, $id = null){
		$request = $this->container->get("request");
		$data = $request->getContent();
		if(empty($data) || !$data)
			throw new \Exception("No data for update");
		$data = json_decode($data, true);
		if(!is_null($id)){
			$metadata = $this->em->getClassMetadata($className);
			$idField = $metadata->identifier[0];
			$data[$idField] = $id;
		}
		$newClass = $this->container->get("talker")->denormalizeEntity($className, $data);
		return $newClass;
	}
	
	// +++++++++++++++++++ SAVE +++++++++++++++++++++++++++++
	public function doSave(&$entity) {
		if(is_array($entity)){
			foreach($entity as $ent){
				$this->em->persist($ent);
			}
		}else{
			$this->em->persist($entity);
		}
		$validator = $this->container->get("validator");
		$errors = $validator->validate($entity);
		if (count($errors) > 0) {
			throw new RuntimeException($errors->__toString());
		}
		// echo $entity->getCompany()->getId();
		$this->em->flush();
		
		return $entity;
	}
	
	// +++++++++++++++++++ UPDATE +++++++++++++++++++++++++++++
	public function doUpdate(&$entity) {
		$validator = $this->container->get("validator");
		$errors = $validator->validate($entity);
		if (count($errors) > 0) {
			throw new RuntimeException($errors->__toString());
		}
		$this->em->flush();
		//echo $entity;
		return $entity;
	}

	// +++++++++++++++++++ DELETE +++++++++++++++++++++++++++++
	public function doDelete($entity) {
		if(is_array($entity)){
			foreach($entity as $ent){
				$ent->setDeleted(true);
			}
		}else{
			$entity->setDeleted(true);
		}
		$this->em->flush();
	}
	
	public function applyPagination(&$data, $count, $start, $limit) {
		$paginateData = array ();
		for($i = $start; $i < ($start + $limit); $i ++) {
			if ($count > $i) {
				$paginateData [] = $data [$i];
			}
		}
		$data = $paginateData;
		return $data;
	}
	
	public function generatePassword($l) {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = "";
		for($i = 0; $i < $l; $i ++) {
			$n = rand ( 0, strlen ( $alphabet ) - 1 );
			$pass .= $alphabet [$n];
		}
		return $pass;
	}

	public function setInfoToLog($entityName, $entityPost, $entityDeleted = null){
        $request = $this->container->get("request");
        $method = $request->getMethod();
        $logService = $this->container->get("transactionLog");  
        $user = $this->container->get('security.context')->getToken()->getUser();
        switch($method){
            case 'POST':
                $data = $this->container->get("talker")->getData();
                $logService->write($entityName, "", "", "", $user->getId(), "POST", $entityPost->getId());
                break;
            case 'PUT':
                foreach ($this->propertyChanged as $item){
                    //$logService->write($entityName, $item["propertyName"], $item["oldValue"], $item["newValue"], $user->getId(), "PUT", $item["entityId"]);
                }
                break;
            case "DELETE":
                if($entityDeleted)
                    $logService->write($entityName, "", $entityDeleted->getId(), "", $user->getId(), "DELETE", $entityDeleted->getId());
                break;
        }
    }
}