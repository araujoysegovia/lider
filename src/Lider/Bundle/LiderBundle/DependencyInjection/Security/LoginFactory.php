<?php

namespace Lider\Bundle\LiderBundle\DependencyInjection\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;


class LoginFactory extends AbstractFactory
{
	/*public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
	{
		/*$providerId = 'security.authentication.provider.login.'.$id;
		$container
		->setDefinition($providerId, new DefinitionDecorator('login.security.authentication.provider'))
		->replaceArgument(0, new Reference($userProvider))
		;
	
		$listenerId = 'security.authentication.listener.login.'.$id;
		$listener = $container->setDefinition($listenerId, new DefinitionDecorator('login.security.authentication.listener'));
	
		return array($providerId, $listenerId, $defaultEntryPoint);
	}*/
	
	public function create(ContainerBuilder $container, $id, $config, $userProviderId, $defaultEntryPointId)
	{
		if($config["check_path"]){
			$this->addOption("check_path", $config["check_path"]);
		}
		if($config["default_target_path"]){
			$this->addOption("default_target_path", $config["default_target_path"]);
		}
		//$this->addOption($name, $value);
		
		// authentication provider
		$authProviderId = $this->createAuthProvider($container, $id, $config, $userProviderId);
	
		// authentication listener
		$listenerId = $this->createListener($container, $id, $config, $userProviderId);
	
		return array($authProviderId, $listenerId, $defaultEntryPointId);
	}
	
	public function getPosition()
	{
		return 'http';
	}
	
	public function getKey()
	{
		return 'elogin';
	}
	
	public function addConfiguration(NodeDefinition $node)
	{
		parent::addConfiguration($node);
		
		$builder = $node->children();
		$builder->scalarNode('login_path')->cannotBeEmpty()->isRequired()->end();
		$builder->scalarNode('check_path')->cannotBeEmpty()->isRequired()->end();
		$builder->scalarNode('default_target_path')->cannotBeEmpty()->isRequired()->end();
	}
	
	
	/**
	 * {@inheritDoc}
	 */
	protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
	{
		$providerId = 'security.authentication.provider.login.'.$id;
				
		$container
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
		$listenerId = parent::createListener($container, $id, $config, $userProvider);
		/*$listener = $container->getDefinition($listenerId)
			->addMethodCall('setCheckPath', array($config["check_path"]));*/
	
		return $listenerId;
	}
	
	protected function getListenerId()
	{
		return 'login.security.authentication.listener';
	}
}