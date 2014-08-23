<?php 
namespace Lider\Bundle\LiderBundle\Security\Core;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;


use Lider\Bundle\LiderBundle\Entity\Player;

class UserProvider implements UserProviderInterface
{
	private $container;
	
	public function __construct($container){
		$this->container = $container;
	}
	
	public function loadUserByUsername($username)
	{
		$request = $this->container->get("request");	
		$user = $this->container->get('doctrine')->getEntityManager()
				->getRepository("LiderBundle:Player")
				->findOneBy(array("email" => $username, "deleted" => false));
		if($user)
			return $user;
	
		throw new UsernameNotFoundException(
				sprintf('Username "%s" does not exist.', $username)
		);
	}
	
	public function loadSessionByToken($token)
	{
		$request = $this->container->get("request");
		$session = $this->container->get('doctrine_mongodb')->getManager()
			->getRepository("LiderBundle:Session")
			->findOneBy(array("email"=> $token->getUsername(), "token" => $token->accessToken, "enabled" => true));
		
		if ($session) {			
			return $session;
		}
	
		throw new UsernameNotFoundException(
				sprintf('Username "%s" does not exist.', $token->accessToken)
		);
	}
	
	public function refreshUser(UserInterface $user)
	{
		if (!$user instanceof Player) {
			throw new UnsupportedUserException(
					sprintf('Instances of "%s" are not supported.', get_class($user))
			);
		}
	
		return $this->loadUserByUsername($user->getUsername());
	}
	
	public function supportsClass($class)
	{
		return $class === 'Lider\Bundle\LiderBundle\Entity\Player';
	}
}