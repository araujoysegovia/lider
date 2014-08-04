<?php

namespace Lider\Bundle\LiderBundle\DependencyInjection\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;



class HeaderAuthenticationFactory extends AbstractFactory
{
	public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
    	
        $providerId = 'security.authentication.provider.hauth.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('hauth.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        //$listenerId = 'security.authentication.listener.hauth.'.$id;
        //$listener = $container->setDefinition($listenerId, new DefinitionDecorator('hauth.security.authentication.listener'));
        
        // authentication listener
        $listenerId = $this->createListener($container, $id, $config, $userProvider);

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'hauth';
    }
    
    /**
     * {@inheritDoc}
     */
    protected function createListener($container, $id, $config, $userProvider)
    {
    	$listenerId = parent::createListener($container, $id, $config, $userProvider);
    	/*$listener = $container->getDefinition($listenerId)
    	 ->addMethodCall('setCheckPath', array($config["check_path"]));*/
    
    	return $listenerId;
    }
    
    /**
     * {@inheritDoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
    	$providerId = 'security.authentication.provider.login.'.$id;
    
    	$container
    	->setDefinition($providerId, new DefinitionDecorator('hauth.security.authentication.provider'))
    	->addArgument(new Reference($userProviderId))
    	->addArgument(new Reference("security.encoder_factory"));
    
    	return $providerId;
    }
    
    protected function getListenerId()
    {
    	return 'hauth.security.authentication.listener';
    }

    public function addConfiguration(NodeDefinition $node){}
}