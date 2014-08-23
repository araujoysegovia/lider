<?php

namespace Lider\Bundle\LiderBundle\DependencyInjection\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;



//class HeaderAuthenticationFactory extends AbstractFactory
class HeaderAuthenticationFactory extends AbstractFactory
{
	public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
    	
    	//echo $userProvider;
    	
        /*$authProviderId = 'security.authentication.provider.hauth.'.$id;
        $container
            ->setDefinition($authProviderId, new DefinitionDecorator('hauth.security.authentication.provider'))
            ->replaceArgument(0, new Reference("lider_user_provider"))
        ;*/
    	
    	$authProviderId = $this->createAuthProvider($container, $id, $config, $userProvider);

        //$listenerId = 'security.authentication.listener.hauth.'.$id;
        //$listener = $container->setDefinition($listenerId, new DefinitionDecorator('hauth.security.authentication.listener'));
        
        
    	
    	// authentication listener
    	$listenerId = $this->createListener($container, $id, $config, $userProvider);
    	
    	return array($authProviderId, $listenerId, $defaultEntryPoint);

        //return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'hauth';
    }
    
    public function addConfiguration(NodeDefinition $node){}
    
    
    /**
     * {@inheritDoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
    	$providerId = 'security.authentication.provider.hauth.'.$id;
    	
    	//echo "<br>".$userProviderId;
    	
    	$container
    		->setDefinition($providerId, new DefinitionDecorator('hauth.security.authentication.provider'))
    		->addArgument(new Reference("lider_user_provider"))
    		->addArgument(new Reference("security.encoder_factory"));
    
    	/*$container
    	->setDefinition($providerId, new DefinitionDecorator('login.security.authentication.provider'))
    	->addArgument(new Reference($userProviderId))
    	->addArgument(new Reference("security.encoder_factory"));
    
    	/*$this->createResourceOwnerMap($container, $id, $config);
    
    	$container
    	->setDefinition($providerId, new DefinitionDecorator('hwi_oauth.authentication.provider.oauth'))
    	->addArgument($this->createOAuthAwareUserProvider($container, $id, $config['oauth_user_provider']))
    	->addArgument($this->getResourceOwnerMapReference($id))
    	->addArgument(new Reference('hwi_oauth.user_checker'))
    	;*/
    
    	return $providerId;
    }
    
    
    /**
     * {@inheritDoc}
     */
    protected function createListener($container, $id, $config, $userProvider)
    {
    	//echo "ENTRO".$config["check_path"];
    	$listenerId = parent::createListener($container, $id, $config, $userProvider);
    	/*$listener = $container->getDefinition($listenerId)
    	 ->addMethodCall('setCheckPath', array($config["check_path"]));*/
    
    	return $listenerId;
    }
    
    protected function getListenerId()
    {
    	return 'hauth.security.authentication.listener';
    }
    
    
    
    /*
    protected function createListener($container, $id, $config, $userProvider)
    {
    	$listenerId = parent::createListener($container, $id, $config, $userProvider);
    
    	return $listenerId;
    }
    
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

 	*/  
    
    
}