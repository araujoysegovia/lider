<?php 
namespace Lider\Bundle\LiderBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UserToken extends AbstractToken
{
	public $accessToken;
	
	public function __construct(array $roles = array())
	{
		parent::__construct($roles);
		//$this->setAuthenticated(count($roles) > 0);
	}
	
	public function getCredentials()
	{
		return '';
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
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(array(
				$this->accessToken,
				parent::serialize()
		));
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized)
	{
		list($this->accessToken,
			$parentStr) = unserialize($serialized);
		parent::unserialize($parentStr);
	}	
}

?>