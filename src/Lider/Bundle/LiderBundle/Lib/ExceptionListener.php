<?php
namespace Lider\Bundle\LiderBundle\Lib;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $code = $exception->getCode();

        if($code == intval(0) || $code == "0" || $code == "")
        {
            $code = 500;
        }
        
        $format = array(
            "message" => $exception->getMessage(),
            "code" => $code
        );
        $type = $this->request->getType();
        $response = new Response();
        $response->setContent(json_encode($format));
        $response->headers->set('Content-Type', $type['type']);
        if ($exception instanceof HttpExceptionInterface) {
            $response->headers->replace($exception->getHeaders());
        }
            $response->setStatusCode($code);
        $event->setResponse($response);
    }
}