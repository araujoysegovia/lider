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
use Lider\Bundle\LiderBundle\Security\Authentication\Token\HeaderUserToken;

class HeaderAuthenticationListener implements ListenerInterface
{

	protected $options;
    protected $logger;
    protected $authenticationManager;
    protected $providerKey;
    protected $httpUtils;

    private $securityContext;
    private $sessionStrategy;
    private $dispatcher;
    private $successHandler;
    private $failureHandler;
    private $rememberMeServices;
	
	public function __construct(SecurityContextInterface $securityContext, 
			AuthenticationManagerInterface $authenticationManager, 
			$sessionStrategy, 
			HttpUtils $httpUtils, 
			$providerKey, 
			$successHandler, 
			$failureHandler, 
			array $options = array(), 
			$logger = null, 
			$dispatcher = null){
		
		$this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->sessionStrategy = $sessionStrategy;
        $this->providerKey = $providerKey;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->options = array_merge(array(
            'check_path'                     => '/login_check',
            'login_path'                     => '/login',
            'always_use_default_target_path' => false,
            'default_target_path'            => '/',
            'target_path_parameter'          => '_target_path',
            'use_referer'                    => false,
            'failure_path'                   => null,
            'failure_forward'                => false,
            'require_previous_session'       => true,
        ), $options);
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->httpUtils = $httpUtils;
		//parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, $options, $logger, $dispatcher);
	}
	
	/*public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
	{
		$this->securityContext = $securityContext;
		$this->authenticationManager = $authenticationManager;
		$this->httpUtils = new HttpUtils();
	}*/
	
	protected function requiresAuthentication(Request $request)
	{
		if ($request->isMethod('OPTION') || $request->isMethod('OPTIONS')) {	
			return false;
		}
		
		//echo rawurldecode($request->getPathInfo());
		return !$this->httpUtils->checkRequestPath($request, "/admin/check/google") && 
				!$this->httpUtils->checkRequestPath($request, "/admin/check") &&
				!$this->httpUtils->checkRequestPath($request, "/login");
				
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function handle(GetResponseEvent $event)
    {
    	
    	$request = $event->getRequest(); 
    	
        if (!$this->requiresAuthentication($request)) {
        	return;
        }
        
		$regexByUserAndPassword = '/Username Username="([^"]+)", Password="([^"]+)"/';
		$regexByUserAndToken = '/Username Username="([^"]+)", Token="([^"]+)"/';
		if ($request->headers->has('x-login') && 1 === preg_match($regexByUserAndPassword, $request->headers->get('x-login'), $matches)) {
			$token = new HeaderUserToken();
			$userName = $matches[1];
			$token->setUser($userName);
			$token->digest = $matches[2];
		}else if($request->headers->has('x-login') && 1 === preg_match($regexByUserAndToken, $request->headers->get('x-login'), $matches)){
			$token = new HeaderUserToken();
			$userName = $matches[1];
			$token->setUser($userName);
			$token->accessToken = $matches[2];
		}else{
			return;
		}
		//$request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $userName);
		
		/*$t = $this->authenticationManager->authenticate($token);
		$this->securityContext->setToken($token);
		return;*/
		
		try {
		    $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);
            
		    return;
		} catch (AuthenticationException $failed) {}

		$response = new Response("Usuario No Registrado");
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		
		$event->setResponse($response);
	}
}