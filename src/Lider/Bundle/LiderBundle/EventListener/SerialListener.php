<?php
namespace Lider\Bundle\LiderBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class SerialListener
{
	private $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
	}
}