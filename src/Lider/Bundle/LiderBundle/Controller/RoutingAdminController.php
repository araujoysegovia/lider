<?php

namespace Lider\Bundle\LiderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class RoutingAdminController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LiderBundle:Default:index.html.twig', array('name' => $name));
    }


    /**   
     * @Template("LiderBundle:LiderAdmin:index.html.twig")
     */
    public function loginAdminAction(Request $request)
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

    	//return $this->render('LiderBundle:LiderAdmin:index.html.twig');
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
     * @Template("LiderBundle:LiderAdmin:home.html.twig")
     */
    public function homeAdminAction(Request $request)
    {    
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
		return array("user" => $user);
    	//return $this->render('LiderBundle:Lider:index.html.twig');
    }
}
