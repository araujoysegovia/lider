<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Lider\Bundle\LiderBundle\Lib\Normalizer;

class QuestionManager
{
	private $em;
	private $dm;
	private $co;

	public function __construct($em, $dm, $co){
		$this->em = $em;
		$this->dm = $dm;
		$this->co = $co;
	}

	public function endDuels(){
		$repo = $this->em->getRepository("LiderBundle:Duel");
		$list = $repo->findDuelExpired(new \DateTime());
		foreach($list as $key => $value)
		{
			$value->setActive(false);
		}
		$this->em->flush();
	}
}
?>