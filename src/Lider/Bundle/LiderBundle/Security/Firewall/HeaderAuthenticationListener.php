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

class HeaderAuthenticationListener extends AbstractAuthenticationListener
{

	
	protected function requiresAuthentication(Request $request)
	{
		return !$this->httpUtils->checkRequestPath($request, "/admin/login-check/google") && !$this->httpUtils->checkRequestPath($request, "/admin/login-check");
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function attemptAuthentication(Request $request)
	{
		$regexByUserAndPassword = '/Username Username="([^"]+)", Password="([^"]+)"/';
		$regexByUserAndToken = '/Username Username="([^"]+)", Token="([^"]+)"/';
		if ($request->headers->has('x-login') && 1 === preg_match($regexByUserAndPassword, $request->headers->get('x-login'), $matches)) {
			$token = new UserToken();
			$token->setUser($matches[1]);
			$token->getDigest($matches[2]);
			$userName = $matches[1];
		}else if($request->headers->has('x-login') && 1 === preg_match($regexByUserAndToken, $request->headers->get('x-login'), $matches)){
			$token = new UserToken();
			$token->setUser($matches[1]);
			$token->setAccessToken($matches[2]);
			$userName = $matches[1];
		}else{
			return;
		}
	
		$request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $userName);
	
		$t = $this->authenticationManager->authenticate($token);
	
		return $t;
	}
	
	private function onFailure(Request $request, AuthenticationException $failed)
	{
	
		$token = $this->securityContext->getToken();
		if ($token instanceof UsernamePasswordToken && $this->providerKey === $token->getProviderKey()) {
			$this->securityContext->setToken(null);
		}
	
		$response = $this->failureHandler->onAuthenticationFailure($request, $failed);
	
		if (!$response instanceof Response) {
			throw new \RuntimeException('Authentication Failure Handler did not return a Response.');
		}
	
		return $response;
	}
}