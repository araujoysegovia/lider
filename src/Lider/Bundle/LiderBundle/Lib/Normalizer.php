<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;


class Normalizer extends GetSetMethodNormalizer
{
    private $em;
    
    private $ignoreProperties = array("id", "entrydate", "lastupdate");
    
    public function setEntityManager($em) {
        $this->em = $em;
    }

    private function isGetMethod(\ReflectionMethod $method)
    {
        return (
            0 === strpos($method->name, 'get') &&
            3 < strlen($method->name) &&
            0 === $method->getNumberOfRequiredParameters()
        );
    }

    public function clearIgnoredAttributes()
    {
        $this->ignoredAttributes = array();
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array(), $recursive = true, $rn = 0)
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);
        $attributes = array();
        $objectName = $reflectionObject->getName();
        if(substr($objectName, 0,1)!="\\") $objectName = "\\".$objectName;
        $md = $this->em->getClassMetadata($objectName);
        $associations = $md->associationMappings;
        foreach ($reflectionMethods as $method) {
            if ($this->isGetMethod($method)) {
                $attributeName = lcfirst(substr($method->name, 3));
                if (in_array($attributeName, $this->ignoredAttributes)) continue;                
                $attributeValue = $method->invoke($object);
                if (array_key_exists($attributeName, $this->callbacks)) 
                	$attributeValue = call_user_func($this->callbacks[$attributeName], $attributeValue);
                if (null !== $attributeValue && !is_scalar($attributeValue)) {
                    if(array_key_exists($attributeName, $associations)){
                        $me = $associations[$attributeName];                            
                        if($me["type"] == 4 || $me["type"] == 8){
                            $entities = array();
                            if($recursive){
                                foreach($attributeValue as $entity)
                                {
                                    if($me["type"] == 8)
                                    	$entities[] = $this->normalize($entity, $format, $context, false, $rn++);
                                    else
                                        $entities[] = $this->normalize($entity, $format, $context, true, $rn++);                                    
                                }
                                $attributeValue = $entities;
                            }
                        }
                        else
                        {
                            if($recursive)
                                $attributeValue = $this->normalize($attributeValue, $format, $context, false, $rn++);
                        }
                        
                    }
                }
                $attributes[$attributeName] = $attributeValue;
            }
        }

        return $attributes;
    }

    /**
     * @param unknown_type $data
     * @param unknown_type $class
     * @param unknown_type $format
     * @throws RuntimeException
     * @return Ambigous <unknown, object>
     */
    public function denormalize($data, $class, $format = null, array $context = array()) {
        // TODO: Auto-generated method stub
        $reflectionClass = new \ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor) {
            $constructorParameters = $constructor->getParameters();
            $params = array();
            foreach ($constructorParameters as $constructorParameter) {
                $paramName = strtolower($constructorParameter->getName());
                if (isset($data[$paramName])) {
                    $params[] = $data[$paramName];
                    unset($data[$paramName]);
                } elseif (!$constructorParameter->isOptional()) {
                    throw new RuntimeException(
                            'Cannot create an instance of ' . $class .
                            ' from serialized data because its constructor requires ' .
                            'parameter "' . $constructorParameter->getName() .
                            '" to be present.');
                }
            }
            $newClass = $reflectionClass->newInstanceArgs($params);
        } else {
            $newClass = new $class;
        }
             
        $em = $this->em;        
        $metadata = $em->getClassMetadata($class);
        $associations = $metadata->associationMappings;
        $fieldMapping = $metadata->fieldMappings;
        
        if(is_numeric($data)){
        	return $em->getRepository($class)->find($data);
        }
        
        if(is_array($data) && array_key_exists($metadata->identifier[0], $data) &&
        	!is_null($data[$metadata->identifier[0]]) && !empty($data[$metadata->identifier[0]])){
        	$newClass = $em->getRepository($class)->find($data[$metadata->identifier[0]]);
        }
        
        foreach ($data as $key => $value) {
        	if(in_array($key, $this->ignoreProperties)) continue;
        	if(!empty($value) || is_bool($value)){
        		$setter = "set" . ucwords ( $key );
				if (array_key_exists ( $key, $associations )) {
					
					$asso = $associations [$key];
					
					$entity = $asso["targetEntity"];
					$mappedBy = $asso["mappedBy"];
					$mbSetter = null;
					if($asso ["type"] == 4)
						$mbSetter = "set" . ucwords ( $mappedBy );
					
					if($asso ["type"] == 8 && !empty($mappedBy))
						if (substr ( $mappedBy, - 1 ) == "s")
							$mbSetter = 'add' . ucwords ( substr ( $mappedBy, 0, - 1 ) );
						else
							$mbSetter = 'add' . ucwords ( $mappedBy );
												
					//echo "\n\n\n --------------------------------- Voy a entrear a obtener $entity --------------------------------\n\n";
					if (is_array($value)){
						$keys = array_keys ($value);
						if(is_numeric ( $keys [0] )) {
							$obj = array ();
							foreach ( $value as $v ) {
								
								$newObj = $this->denormalize ( $v, $entity, $format );
								if(!is_null($mbSetter)) $newObj->$mbSetter($newClass);
								$obj [] = $newObj;
							}
						}else{
							$obj = $this->denormalize ( $value, $entity, $format);
							if(!is_null($mbSetter)) $obj->$mbSetter($newClass);
						}
					}else{
						$obj = $this->denormalize ( $value, $entity, $format );
						if(!is_null($mbSetter)) $obj->$mbSetter($newClass);
					}
					
					//echo "\n\n\n --------------------------------- FIN --------------------------------\n\n";
					
					if ($asso ["type"] == 4 || $asso ["type"] == 8) {
						if (substr ( $asso ["fieldName"], - 1 ) == "s")
							$setter = 'add' . ucwords ( substr ( $key, 0, - 1 ) );
						else
							$setter = 'add' . ucwords ( $key );
						
						$getter = 'get' . ucwords ( $key );
						$collection = $newClass->$getter();
						$foundInCollection = $this->lookInCollection ( $obj, $collection);
						$toDelete = $this->removeNotBe($obj, $collection);
						
						if (substr ( $asso ["fieldName"], - 1 ) == "s")
							$removeMethod = 'remove' . ucwords ( substr ( $key, 0, - 1 ) );
						else
							$removeMethod = 'remove' . ucwords ( $key );

						foreach ($toDelete as $key => $ent) {
							$newClass->$removeMethod($ent);
							if($asso ["type"] == 4){
								$em->remove($ent);
							}
						}
						
						if(!is_array($foundInCollection) && !$foundInCollection){
							if(is_array($obj)){
								foreach ($obj as $item) {
									$newClass->$setter($item);
								}
							}else{
								$newClass->$setter($obj);
							}
						}else if(is_array($foundInCollection)){
							foreach ($foundInCollection as $k => $o) {
								if(!$o){
									foreach ($obj as $item) {
										if($k == $item->getId())
											$newClass->$setter($item);
									}
								}
							}
						}
						
					}else{
						if (method_exists ( $newClass, $setter )) {
							$newClass->$setter($obj);
						}	
					}
					
				} elseif (array_key_exists ( $key, $fieldMapping )) {
					if ($fieldMapping [$key] ["type"] == "datetime" || $fieldMapping [$key] ["type"] == "date") {
						$date = $value;
						if(is_array($date) && array_key_exists("date", $date)){
							$value = new \DateTime ( $date["date"] );
						}elseif(is_string($date)){
							$value = new \DateTime ( $date );
						}else{
							$value = new \DateTime ();
						}
					}
					
					if (method_exists ( $newClass, $setter )) {
						if(is_bool($value) && !$value) $value = "false";
						$newClass->$setter ( $value );
					}
				}
        	}
        }
        $em->persist($newClass);
        return $newClass;
    }
    
    private function removeNotBe($entity, &$coll){
    	$em =  $this->em;
    	$toDelete = array();
    	$items = null;
    	if(is_array($coll)){
    		$items = $coll;
    	}else{
    		$items = $coll->toArray();
    	}
    	
    	foreach ($items as $key => $obj) {
    		if(is_array($entity)){
    			$found = false;
    			foreach ($entity as $ent) {
    				if($obj->getId() == $ent->getId()){
    					$found=true;
    					break;
    				}
    			}
    			if(!$found){
    				$toDelete[$key] = $obj;
    			}
    		}else{
    			if($obj->getId() != $entity->getId()){
    				$toDelete[$key] = $obj;
    			}
    		}
    	}
    	
    	/*foreach ($toDelete as $key => $obj) {
    		$coll->removeElement($obj);
    		//$em->remove($obj);
    	}*/
    	return $toDelete;
    }
    
    private function lookInCollection(&$entity, $coll){
    	$em =  $this->em;
    	if(is_array($entity)){
    		$e = array();
    		foreach ($entity as $o) {
    			$e[$o->getId()] = $this->lookInCollection($o, $coll);
    		}
    		return $e;
    	}else{
    		$count = 0;
    		if(is_array($coll)){
    			$count = count($coll);
    		}else{
    			$count = $coll->count();
    		}
    		if($count > 0 && !is_null($entity->getId())){
    			//echo "<br>Voy a buscar ".$entity->getId();
    			$found = false;
    			//echo $coll;
    			if(!is_array($coll)){
    				foreach ($coll->toArray() as $item){
    					if($item->getId() == $entity->getId()){
    						$found = true;
    						break;
    					}
    				}
    			}
    			
    			//echo $found ? "true" : "false";
    			return $found;
    			//$item = $coll->get($entity->getId());
    			
    			
    			//echo is_null($item) ? " Encontrado " : " No encontrado ";
    			
    			/*if(!is_null($item)){
    				$em = $this->em;       
    				$class = get_class($entity);  
			        $metadata = $em->getClassMetadata($class);
			        $associations = $metadata->associationMappings;
			        $fieldMapping = $metadata->fieldMappings;
    				foreach ($fieldMapping as $field) {
    					if(is_array($field)){
    						if(array_key_exists("fieldName", $field)){
    							$setter = "set" . ucwords($field["fieldName"]);
    						}
    					}else{
    						$setter = "set" . ucwords($field);
    					}
    				}
    			}*/
    		}
    		return false;
    	}
    }
    
}
?>