<?php 
namespace Lider\Bundle\LiderBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UserToken extends AbstractToken
{
	private $digest;	
	private $externalLogin = false;
	
	public function __construct(array $roles = array())
	{
		parent::__construct($roles);
		$this->setAuthenticated(count($roles) > 0);
	}
	
	public function getCredentials()
	{
		return '';
	}
	
	public function setExternalLogin($elogin){
		$this->externalLogin = $elogin;
	}
	
	public function isExternalLogin(){
		return $this->externalLogin;
	}
	
	public function setDigest($digest){
		$this->digest = $digest;
	}
	
	public function getDigest(){
		return $this->digest;
	}
	
}

?>