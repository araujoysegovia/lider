<?php 
namespace Lider\Bundle\LiderBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UserToken extends AbstractToken
{
	private $digest;	
	private $externalLogin = false;
	private $accessToken;
	
	public function __construct(array $roles = array())
	{
		parent::__construct($roles);
		//$this->setAuthenticated(count($roles) > 0);
	}
	
	public function getCredentials()
	{
		return '';
	}
	
	public function setDigest($digest){
		$this->digest = $digest;
	}
	
	public function getDigest(){
		return $this->digest;
	}
	
	/**
	 * @param string $accessToken The OAuth access token
	 */
	public function setAccessToken($accessToken)
	{
		$this->accessToken = $accessToken;
	}
	
	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function serialize()
	{
		return serialize(array(
				$this->accessToken,
				parent::serialize()
		));
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function unserialize($serialized)
	{
		$data = unserialize($serialized);
		list(
			$this->accessToken,
			$parent,
		) = $data;
	
		parent::unserialize($parent);
	}
	
}

?>