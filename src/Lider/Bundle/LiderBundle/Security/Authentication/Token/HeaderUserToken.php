<?php 
namespace Lider\Bundle\LiderBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class HeaderUserToken extends AbstractToken
{
	public $digest;	
	public $accessToken;

	public function __construct(array $roles = array())
	{
		parent::__construct($roles);
		$this->setAuthenticated(count($roles) > 0);
	}
	
	public function getCredentials()
	{
		return '';
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(array(
				$this->digest, 
				$this->accessToken,
				parent::serialize()
		));
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized)
	{
		list($this->digest,
			$this->accessToken,
			$parentStr) = unserialize($serialized);
		parent::unserialize($parentStr);
	}
}

?>