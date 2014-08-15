<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\ControllerController;
use Symfony\Component\HttpFoundation\Request;

class PlayerController extends Controller
{
    public function getName(){
    	return "Player";
    }

    /**
     * Codifica el password usando el Salt del usuario
     * 
     * @param password - Password a codificar
     * @return password - Paswword codificado
     */
    private function generatePass($password)
    {
        $user = new \Lider\Bundle\LiderBundle\Entity\Player();
        $factory = $this->container->get('security.encoder_factory');
        $codificador = $factory->getEncoder($user);
        $password = $codificador->encodePassword($password, $user->getSalt());
        return $password;
    }

    public function loginAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $repo = $em->getRepository("LiderBundle:Player");
        $repoMongo = $dm->getRepository("LiderBundle:Session");
        $request = $this->get("request");

        if(!is_object($user))
        {
            if($request->headers->has("X-USER") && $request->headers->has("X-PASS"))
            {

                $sifincaUser = '/SessionUser Username="([^"]+)"/';
                if(preg_match($sifincaUser, $request->headers->get('X-USER'), $matches)) {
                    $userName = $matches[1];
                }
                $sifincaPass = '/SessionPass Password="([^"]+)"/';
                if(preg_match($sifincaPass, $request->headers->get('X-PASS'), $matches)) {
                    $password = $matches[1];
                }
            }
            else
            {
                if(isset($_GET['user']) && isset($_GET['pass']))
                {
                    $userName = $_GET['user'];
                    $password = $_GET['pass'];
                }
            }
            // echo $userName." ".$password;
            
            if(isset($userName) && isset($password))
            {
                $pass = $this->generatePass($password);
                $user = $repo->findOneByEmail($userName);
                if(!$user || $user->getPassword() != $pass)
                {
                    throw new \Exception("User Not Registered", 403);
                }
            }
            else
            {
                throw new \Exception("User Not Registered", 403);
            }
        }
        else{
            $userName = $user->getEmail();
        }
        $sess = $repoMongo->findOneBy(array("userAgent" => $request->headers->get('User-Agent'), 
            "email" => $userName, 
            "enabled" => true, 
            "cookie" => $request->headers->get("Cookie")));

        if($sess)
        {
            $session = $sess;
        }
        else
        {
            $session = new \Lider\Bundle\LiderBundle\Document\Session();
            $session->setStart(new \MongoDate());
            $session->setUserId($user->getId());
            $session->setIp($request->getClientIp());
            $session->setLast(new \MongoDate());
            $session->setEnabled(true);
            $session->setEmail($user->getUsername());
            $session->setUserAgent($request->headers->get("User-Agent"));
            $session->setCookie($request->headers->get("Cookie"));
            $dm->persist($session);
            $dm->flush();
            $generateToken = $this->generatePass($session->getId());
            $token = "";
            if(strstr($generateToken, '/'))
            {
                $explode = explode("/", $generateToken);
                foreach($explode as $value)
                {
                    $token .= $value;
                }
            }
            else{
                $token = $generateToken;
            }
            $session->setToken($token);
            $dm->persist($session);
            $dm->flush();
        }
        $arr = array();
        $arr['token'] = $session->getToken();
        $arr['user'] = array(
            "email" => $user->getEmail(),
            "name" => $user->getName(),
            "latname" => $user->getLastname(),
            "image" => $user->getImage(),
            "office" => $user->getOffice(),
            "roles" => $user->getRoles(),
            "team" => $user->getTeam(),
        );
        
        //$list = $repo->getArrayEntityWithOneLevel(array("id" => $user->getId()));
        return $this->get("talker")->response($arr);
    }
}
