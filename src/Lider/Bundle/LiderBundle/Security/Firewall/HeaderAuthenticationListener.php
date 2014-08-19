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

	protected $securityContext;
	protected $authenticationManager;
	protected $httpUtils;
	
	public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
	{
		$this->securityContext = $securityContext;
		$this->authenticationManager = $authenticationManager;
		$this->httpUtils = new HttpUtils();
	}
	
	protected function requiresAuthentication(Request $request)
	{
		//echo "/admin/login-check/google === ".rawurldecode($request->getPathInfo());
		return !$this->httpUtils->checkRequestPath($request, "/admin/check/google") && 
				!$this->httpUtils->checkRequestPath($request, "/admin/check") &&
				!$this->httpUtils->checkRequestPath($request, "/admin/logout");
				
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
			$token->setUser($matches[1]);
			$token->digest = $matches[2];
			$userName = $matches[1];
		}else if($request->headers->has('x-login') && 1 === preg_match($regexByUserAndToken, $request->headers->get('x-login'), $matches)){
			$token = new HeaderUserToken();
			$token->setUser($matches[1]);
			$token->setAccessToken($matches[2]);
			$userName = $matches[1];
		}else{
			return;
		}
		try {
		    $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);
		    return;
		} catch (AuthenticationException $failed) {}

		$response = new Response();
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		$event->setResponse($response);
	}
}