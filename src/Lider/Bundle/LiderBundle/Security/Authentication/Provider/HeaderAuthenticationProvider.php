<?php 
namespace Lider\Bundle\LiderBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Lider\Bundle\LiderBundle\Security\Authentication\Token\HeaderUserToken;

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

//		echo "\dos\n";
		$user = $this->userProvider->loadUserByUsername($token->getUsername());
		if($user){
			$atoken = $token->accessToken;			
			if($atoken){
				$session = $this->userProvider->loadSessionByToken($token);
				if($session){
					$authenticatedToken = new HeaderUserToken($user->getRoles());
					$authenticatedToken->setUser($user);				
					return $authenticatedToken;
				}
				//echo get_class($this->userProvider);
				//throw new \Exception('The authentication failed.');
				//checkerar la session en la BD
			}else{
				$codificador = $this->encoderFactory->getEncoder($user);
				$password = $codificador->encodePassword($token->digest, $user->getSalt());
				if($password == $user->getPassword()){					
					$authenticatedToken = new HeaderUserToken($user->getRoles());
					$authenticatedToken->setUser($user);
					return $authenticatedToken;
				}
			}
		}
	
		throw new \Exception('The authentication failed.');
	}
	
	public function supports(TokenInterface $token)
	{
		return $token instanceof \Lider\Bundle\LiderBundle\Security\Authentication\Token\HeaderUserToken;
	}
}

?>