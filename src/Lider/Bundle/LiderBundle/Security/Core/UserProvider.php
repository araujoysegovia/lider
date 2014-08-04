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

class UserProvider extends OAuthUserProvider
{
	private $container;
	
	public function __construct($container){
		$this->container = $container;
	}
	
	public function loadUserByUsername($username)
	{
		$request = $this->container->get("request");	
		
		$user = $this->container->get("doctrine")->getEntityManager()
						->getRepository("LiderBundle:Player")
						->findOneBy(array("email"=>$username, "deleted" => false));
		if ($user) {
			return $user;
		}
	
		throw new UsernameNotFoundException(
				sprintf('Username "%s" does not exist.', $username)
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
	
	public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
		$username = $response->getUsername(); /* An ID like: 112259658235204980084 */
		$email = $response->getEmail();
		$nickname = $response->getNickname();
		$realname = $response->getRealName();
		$avatar = $response->getProfilePicture();
		 
		$user = $this->loadUserByUsername($response->getUsername());
		if(!$user)
			throw new UsernameNotFoundException(
					sprintf('Username "%s" does not exist.', $username)
			);
		
		return $user;
	}
}