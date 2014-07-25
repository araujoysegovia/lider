<?php
namespace Lider\Bundle\LiderBundle\Security;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationHandler implements AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception){  
		/*if ($failureUrl = $request->get($this->options['failure_path_parameter'], null, true)) {
             $this->options['failure_path'] = $failureUrl;
        }

        if (null === $this->options['failure_path']) {
            $this->options['failure_path'] = $this->options['login_path'];
        }

        if ($this->options['failure_forward']) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Forwarding to %s', $this->options['failure_path']));
            }

            $subRequest = $this->httpUtils->createRequest($request, $this->options['failure_path']);
            $subRequest->attributes->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

            return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Redirecting to %s', $this->options['failure_path']));
        }*/
        
        return new Response($exception->getMessage());
     }

     public function onLogoutSuccess(Request $request) 
     {
     	$referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
     }
}
?>