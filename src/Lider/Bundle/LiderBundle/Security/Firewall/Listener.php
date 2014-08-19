<?php 
namespace Lider\Bundle\LiderBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Lider\Bundle\LiderBundle\Security\Authentication\Token\UserToken;

class Listener extends AbstractAuthenticationListener
{
	protected $securityContext;
	protected $authenticationManager;
	protected $httpUtils;
	protected $em;
	static $URL_TOKEN = 'https://www.googleapis.com/oauth2/v2/userinfo';
	private $check_path = '/admin/check/google';
	private $talker;
	
	/*public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, \Lider\Bundle\LiderBundle\Lib\Talker $talker)
	{
		$this->securityContext = $securityContext;
		$this->authenticationManager = $authenticationManager;
		$this->httpUtils = new HttpUtils();
		$this->talker = $talker;
	}*/
	
	private function getUserInfo($token)  {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, self::$URL_TOKEN);
		curl_setopt($ch,CURLOPT_POST, 0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"authorization: Bearer $token",
		));
		$data = curl_exec($ch);
		curl_close($ch);
		echo $data;
		return $data;
	}
	
/*	
 * 	public function handle(GetResponseEvent $event)
	{
		$request = $event->getRequest();		
		$token = $request->get("access_token");
		$code = $request->get("code");	
		if($token && $code){
			$content = $this->getUserInfo($token);
			$data = json_decode($content, true);
			if(!is_array($data) || (is_array($data) && !array_key_exists("email", $data)))
				throw new AuthenticationException("User not found");
				
			$userName = $data["email"];			
			$token = new UserToken();
			$token->setExternalLogin(true);
		}else{
			$userName = $request->get("_username");
			$password = $request->get("_password");
			
			$token = new UserToken();
			$token->setExternalLogin(false);
			$token->setDigest($password);
		}
		
		if (!$this->requiresAuthentication($request)) {
			return;
		}
					
		$token->setUser($userName);		
		$authToken = $this->authenticationManager->authenticate($token);
		$this->securityContext->setToken($authToken);
		//$response =  $this->onSuccess($request, $authToken);
		//$event->setResponse($response);
	}	
*/
	protected function requiresAuthentication(Request $request)
	{
		if (!$request->isMethod('POST')) {
			return false;
		}
		//echo $this->check_path." === ".rawurldecode($request->getPathInfo());
		return $this->httpUtils->checkRequestPath($request, $this->check_path);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function attemptAuthentication(Request $request)
	{
		$token = $request->get("access_token");
		$code = $request->get("code");	
		if($token && $code){
			$content = $this->getUserInfo($token);
			$data = json_decode($content, true);
			if(!is_array($data) || (is_array($data) && !array_key_exists("email", $data)))
				throw new AuthenticationException("User not found");
				
			$userName = $data["email"];			
			$token = new UserToken();
			$token->setAccessToken($token);
			
		}else{
			throw new \Exception("Missing access token parameters");
		}
		
		$token->setUser($userName);
		
		$request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $userName);

		$t = $this->authenticationManager->authenticate($token);
		
		return $t;
	}
	
	public function setCheckPath($check){
		$this->check_path = $check;
	}
	
}

?>