<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lider\Bundle\LiderBundle\Document\Image;

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
			
            if(isset($_GET['user']) && isset($_GET['pass'])){
                $userName = $_GET['user'];
                $password = $_GET['pass'];
            }
            
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
        }else{
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
        //echo $user->getTeam()->getId();
        $arr = array();
        $roles = array();
        //print_r($user->getRoles()->getId());
        foreach($user->getRoles() as $key => $value){
            $roles[$key] = array(
                "id" => $value->getId(),
                "name" => $value->getName(),
            );
        }
        $office = array(
           "id" => $user->getOffice()->getId(),
           "name" => $user->getOffice()->getName(),
        );

        $arr['token'] = $session->getToken();
        
        $team = $user->getTeam();
        
        $arr['user'] = array(
            "email" => $user->getEmail(),
            "name" => $user->getName(),
            "latname" => $user->getLastname(),
            "image" => $user->getImage(),
            "office" => $office,
            "roles" => $roles,
            "team" => array(),
        );
        
        if($team){
        	$arr['user']['team'] = array(
        		"id" => $user->getTeam()->getId(),
        		"name" => $user->getTeam()->getName()
        	) ;
        }
        
        //$list = $repo->getArrayEntityWithOneLevel(array("id" => $user->getId()));
        return $this->get("talker")->response($arr);
    }
    
    public function teamUserSessionAction() {
    
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	//echo $user->getId();
    	$em = $this->getDoctrine()->getEntityManager();
     	
    	$entities = $em->getRepository("LiderBundle:Player")->getArrayEntityWithOneLevel(
    					 array("team"=>$user->getTeam()->getId(), "deleted" => false));
    	
    	return $this->get("talker")->response($entities);
    }
    
    public function saveImageAction(){
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$repository = $this->get('doctrine_mongodb')->getManager();    	
    	$imagesRepository = $repository->getRepository('LiderBundle:Image');
    	
    	$image = $imagesRepository->findAll();
    	foreach($image as $im)
    	{
    		$dm->remove($im);
    	}
    	try{
    		$dm->flush();
    	}
    	catch(\Exception $e)
    	{
    
    	}
    	$iconArray = array(
    		array("image" => __DIR__."http://10.102.1.22/lider/web/bundles/lider/images/avatar.png"),
    			//array("image" => __DIR__."../Resources/public/images/avatar.png"),
    	);    	 
    
    	$icons = array();
    	$result = new \finfo();
    
    	$className = self::$NAMESPACE.$this->getName();
    	
    	foreach($iconArray as $value){
    
    		$avatar = new Image();
    		//$fileMetadata = new ImageMetadata();
    		$image = new \Imagick($value['image']);
    		//$fileMetadata->setMimeType($result->file($value['image'], FILEINFO_MIME_TYPE));
    		//$fileMetadata->setSize($image->getImageLength());
    		//$fileMetadata->setExtension($image->getImageFormat());
    		
    		$avatar->setName($image->getClientOriginalName());
    		$avatar->setFile($image->getPathname());
    		$avatar->setMimetype($image->getClientMimeType());
    		$avatar->setEntity($className);
    		$avatar->setEntityId($id);
    		
    		$icon->setImage($value['image']);
    		$icon->setMetadata($fileMetadata);
    		$dm->persist($icon);
    		echo $icon->getId();
    		$icons[] = $icon;
    	}
    	$dm->flush();
    	return $icons;
    }
}
