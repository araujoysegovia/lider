<?php

namespace Lider\Bundle\LiderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Lider\Bundle\LiderBundle\DependencyInjection\Security\LoginFactory;
use Lider\Bundle\LiderBundle\DependencyInjection\Security\HeaderAuthenticationFactory;

class LiderBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
	
		$extension = $container->getExtension('security');
		$extension->addSecurityListenerFactory(new LoginFactory());
		$extension->addSecurityListenerFactory(new HeaderAuthenticationFactory());
	}
}
