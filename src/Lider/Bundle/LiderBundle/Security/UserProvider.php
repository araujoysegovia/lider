<?php 
namespace  Lider\Bundle\LiderBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Lider\Bundle\LiderBundle\Entity\Player;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class UserProvider extends OAuthUserProvider //implements UserProviderInterface, OAuthAwareUserProviderInterface
{
	private $em;
	private $cont;
	
	public function __construct($em, $cont){
		$this->em = $em;
		$this->cont = $cont;
	}
	
    public function loadUserByUsername($username)
    {
    	throw new \Exception('loadByUsername not implemented');
    	$user = $this->em->getRepository("LiderBundle:Player")->findOneByEmail($username);
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
    	
    	$user = $this->em->getRepository("LiderBundle:Player")->findOneByEmail($email);
    	if(!$user)
    		throw new UsernameNotFoundException(
    				sprintf('Username "%s" does not exist.', $username)
    		);
    	return $this->loadUserByUsername($response->getUsername());
    }
}
