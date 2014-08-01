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
		if($token->isAuthenticated()){
			$authenticatedToken = new UserToken($user->getRoles());
			$authenticatedToken->setUser($user);
			return $authenticatedToken;
		}
		$user = $this->userProvider->loadUserByUsername($token->getUsername());
		
		if($user){
			if($token->isExternalLogin()){
				$authenticatedToken = new UsernamePasswordToken($user, $token->getCredentials(), "elogin", $user->getRoles());
				$authenticatedToken->setAttributes($token->getAttributes());
				/*$authenticatedToken = new UserToken($user->getRoles());
				$authenticatedToken->setUser($user);
				$authenticatedToken->setAuthenticated(true);*/
				return $authenticatedToken;
			}else{
				$pass = $token->getDigest();
				//$factory = $this->container->get('security.encoder_factory');
				$codificador = $this->encoderFactory->getEncoder($user);
				$password = $codificador->encodePassword($pass, $user->getSalt());
				if($password == $user->getPassword()){
					$authenticatedToken = new UsernamePasswordToken($user, $token->getCredentials(), "elogin", $user->getRoles());
					$authenticatedToken->setAttributes($token->getAttributes());
					
					/*$authenticatedToken = new UserToken($user->getRoles());
					$authenticatedToken->setUser($user);	
					$authenticatedToken->setAuthenticated(true);*/
					return $authenticatedToken;
				}
			}
		}
	
		/*if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
			$authenticatedToken = new WsseUserToken($user->getRoles());
			$authenticatedToken->setUser($user);
	
			return $authenticatedToken;
		}*/
	
		throw new AuthenticationException('The authentication failed.');
	}
	
	public function supports(TokenInterface $token)
	{
		return $token instanceof UserToken;
	}
}

?>