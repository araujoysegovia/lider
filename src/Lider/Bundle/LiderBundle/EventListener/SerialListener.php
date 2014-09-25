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
		if($entity instanceof \Lider\Bundle\LiderBundle\Entity\Team)
		{
			$gearman = $this->container->get("gearman");
			print_r($entity);
			$result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~notificationTeam', json_encode(array(
				"team" => $entity
			)));
		}
	}
}