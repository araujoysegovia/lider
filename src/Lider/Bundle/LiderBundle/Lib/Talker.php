<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Lider\Bundle\LiderBundle\Lib\Normalizer;

class Talker{
	
	private $serializer;
	private $normalizer;
	private $em;

	public function __construct($em, $co) {
		$this->em = $em;
		$this->container = $co;
		$this->normalizer = new Normalizer();
		$this->normalizer->setEntityManager($em);
		
		$this->serializer = new Serializer(array(
			$this->normalizer),
			array(
					"json" => new JsonEncoder(),
					"xml" => new XmlEncoder()
			)
		);
	}
	
	public function getEntityManager(){
		return $this->em;
	}
	
	public function getRequestData(){
		$request = $this->container->get("request");
		$data = $request->getContent();
		return $data;
	}

	private function getResponse(){
		$encoder = $this->getType();
		$response = new Response();
		foreach ($encoder['content'] as $item)
			$response->headers->set("Content-Type", $item);
		
		return $response;
	}
	
	public function normalizeEntity($entity){
		$encoder = $this->getType();
		if(!is_array($entity)){
			$obj = array();
			foreach ($entity as $item) {
				$obj[] = $this->normalizer->normalize($item, $encoder['type']);
			}			
		}else{
			$obj = $this->normalizer->normalize($entity, $encoder['type']);
		}
		return $obj;
	} 
	
	public function denormalizeEntity($entity, $data){				
		$encoder = $this->getType();		
		$obj = $this->normalizer->denormalize($data, $entity, $encoder['type']);		
		return $obj;
	}

	public function response($value) {
		$encoder = $this->getType();
		$response=$this->getResponse();
		if(is_array($value)){
			$keys = array_keys($value);
			if(count($keys) > 0){
				if(!is_array($value[$keys[0]])){
					if(is_object($value[$keys[0]])){
						$obj = array();
						foreach($value as $v)
						{
							$object = $this->normalizer->normalize($v, $encoder['type']);
							$obj[] = $object;
						}
					}else{
						$obj = $value;
					}
				}else
					$obj = $value;
			}
		}else{
			$obj = $this->normalizer->normalize($value, $encoder['type']);
		}
		
		if (!is_null($encoder) && isset($obj)) {
			//print_r($obj);
			$value = $this->serializer->encode($obj, $encoder['type']);
		}else {
			if(is_array($value)){
				if(count($value) == 0){
					$value = "";
				}
			}else
				$value = (string) $value;
		}
		$response->setContent($value);
		$this->normalizer->clearIgnoredAttributes();
		return $response;
	}

	/**
	 *
	 * @return string | null
	 */
	public function getType() {
		$request = $this->container->get("request");
		$response = array();
		foreach ($request->getAcceptableContentTypes() as $item) {
			//echo "$item <br><br>";
			switch ($item) {
				case 'application/json':
					$response["type"] = "json";
					$response["content"] = array("application/json");
					return $response;
					break;
				case 'application/xml':
					$response["type"] = "xml";
					$response["content"] = array("application/xml",
							"application/xhtml+xml");
					return $response;
					break;
				default:
					$response["type"] = "json";
					$response["content"] = array("application/json");
					return $response;
					break;
			}
		}
		return null;
	}

}

?>
