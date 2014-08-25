<?php 
namespace Lider\Bundle\LiderBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Lider\Bundle\LiderBundle\Security\Authentication\Token\UserToken;

class Provider implements AuthenticationProviderInterface
{
	private $userProvider;
	private $encoderFactory;
	private $cacheDir;
	
	public function __construct(UserProviderInterface $userProvider, $encoderFactory)
	{
		$this->userProvider = $userProvider;
		$this->encoderFactory = $encoderFactory;
	}
	
	public function authenticate(TokenInterface $token)
	{
		$user = $this->userProvider->loadUserByUsername($token->getUsername());
		
		if($user){
			$authenticatedToken = new UserToken($user->getRoles());
			$authenticatedToken->setUser($user);
			$authenticatedToken->setAttributes($token->getAttributes());
			return $authenticatedToken;
		}
	
		throw new AuthenticationException('The authentication failed 123.');
	}
	
	public function supports(TokenInterface $token)
	{
		return $token instanceof \Lider\Bundle\LiderBundle\Security\Authentication\Token\UserToken;
	}
}

?>