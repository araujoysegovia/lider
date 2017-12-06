<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class RoutingAdminController extends SymfonyController
{
    public function indexAction($name)
    {
       // return $this->render('LiderBundle:Default:index.html.twig', array('name' => $name));
    }


    /**
     * @Template("LiderBundle:Administrator:index.html.twig")
     */
    public function loginPageAction(Request $request)
    {
    	if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );

    	//return $this->render('LiderBundle:Administrator:index.html.twig');
    }

    public function loginFailureAction(Request $request)
    {
    	if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
    		$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
    		throw new \Exception($error);
    	} else {
    		$error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
    		throw new \Exception($error);
    	}
    }

    /**
     * @Template("LiderBundle:Administrator:home.html.twig")
     */
    public function homePageAction(Request $request)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
		return array("user" => $user);
    	//return $this->render('LiderBundle:Lider:index.html.twig');
    }
}
