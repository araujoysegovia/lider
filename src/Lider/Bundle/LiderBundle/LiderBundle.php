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

/*https://accounts.google.com/o/oauth2/auth?
scope=email%20profile&
state=%2Fprofile&
redirect_uri=http://soylider.sifinca.net/admin/login-check/google&
response_type=token&
client_id=288359316941-7uff5a28m5f5bvin18ohib499qj40acc.apps.googleusercontent.com
