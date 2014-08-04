<?php 
namespace Lider\Bundle\LiderBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Lider\Bundle\LiderBundle\Security\Authentication\Token\UserToken;

class HeaderAuthenticationProvider implements AuthenticationProviderInterface
{
	private $userProvider;
	private $encoderFactory;
	
	public function __construct(UserProviderInterface $userProvider, $encoderFactory)
	{
		$this->userProvider = $userProvider;
		$this->encoderFactory = $encoderFactory;
	}
	
	public function authenticate(TokenInterface $token)
	{	
		$user = $this->userProvider->loadUserByUsername($token->getUsername());
		if($user){
			$atoken = $token->getAccessToken();
			$authenticatedToken = new UserToken();
			$authenticatedToken->setUser($user);
			$authenticatedToken->setAttributes($token->getAttributes());
			
			if($atoken){
				throw new AuthenticationException('The authentication failed.');
				//checkerar la session en la BD
			}else{
				$codificador = $this->encoderFactory->getEncoder($user);
				$password = $codificador->encodePassword($token->getDigest(), $user->getSalt());
				if($password != $user->getPassword()){
					throw new AuthenticationException('The authentication failed.');
				}
				$authenticatedToken->setRoles($user->getRoles());
				return $authenticatedToken;
			}
		}
	
		throw new AuthenticationException('The authentication failed.');
	}
	
	public function supports(TokenInterface $token)
	{
		return $token instanceof \Lider\Bundle\LiderBundle\Security\Authentication\Token\UserToken;
	}
}

?>