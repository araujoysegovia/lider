<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lider\Bundle\LiderBundle\Document\Image;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class PlayerController extends Controller
{
	
	static $URL_TOKEN = 'https://www.googleapis.com/oauth2/v2/userinfo';
	
    public function getName(){
    	return "Player";
    }
    
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
    	return $data;
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
    
    public function loginWithGoogleAction(Request $request){
    	$em = $this->getDoctrine()->getEntityManager();
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	
    	$atoken = $request->get("access_token");
    	$code = $request->get("code");
    	if(!$atoken || !$code)
    		throw new \Exception("Token not found");
		
    	$content = $this->getUserInfo($atoken);
    	$data = json_decode($content, true);
    	if(!is_array($data) || (is_array($data) && !array_key_exists("email", $data)))
    		throw new AuthenticationException("User not found");
    	
    	$repo = $em->getRepository("LiderBundle:Player");
    	$userName = $data["email"];
    	$user = $repo->findOneByEmail($userName);
    	
    	if(!$user)
    		throw new \Exception("User Not Registered", 403);
    	
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
    	
    	$arr['user'] = array(
    			"email" => $user->getEmail(),
    			"name" => $user->getName(),
    			"latname" => $user->getLastname(),
    			"image" => $user->getImage(),
    			"office" => $office,
    			"roles" => $roles,
    			"team" => array(),
                "changePassword" => $user->getChangePassword()
    	);
    	
    	$team = $user->getTeam();
    	
    	if($team){
    		$arr['user']['team'] = array(
    			"id" => $user->getTeam()->getId(),
    			"name" => $user->getTeam()->getName()
    		) ;
    	}
    	
    	
    	$playerGameInfo = $repo->getPlayerGamesInfo($user->getId());
    	$arr['user']['gameInfo'] = $playerGameInfo;
    	 
        $parameters = $this->get('parameters_manager')->getParameters();

        $arr['config'] = array(
            "timeQuestionPractice" => $parameters['gamesParameters']['timeQuestionPractice'],
            "timeQuestionDuel" => $parameters['gamesParameters']['timeQuestionDuel'],
            "timeGame" => $parameters['gamesParameters']['timeGame'],
            "timeDuel" => $parameters['gamesParameters']['timeDuel']
        );

    	return $this->get("talker")->response($arr);
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
        
        $arr = array();
        $roles = array();        
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
            "changePassword" => $user->getChangePassword(),
            "gameInfo" => array(
                'win' => 0,
                'lost' => 0,
                'points' => 0
            )        
        );
        

        if($team){
        	$arr['user']['team'] = array(
        		"id" => $user->getTeam()->getId(),
        		"name" => $user->getTeam()->getName()        		
        	) ;
        }

        $points = $user->getPlayerPoints()->toArray();

        foreach ($points as $value) {
            if($value->getTournament()->getActive()){
                $arr['user']['win'] += $value->getWin();
                $arr['user']['lost'] += $value->getLost();
                $arr['user']['points'] += $value->getPoints();
            }
        }

        // $playerGameInfo = $repo->getPlayerGamesInfo($user->getId());
        // $arr['user']['gameInfo'] = $playerGameInfo;
        $parameters = $this->get('parameters_manager')->getParameters();

        $arr['config'] = array(
            "timeQuestionPractice" => $parameters['gamesParameters']['timeQuestionPractice'] ,
            "timeQuestionDuel" => $parameters['gamesParameters']['timeQuestionDuel'],
            "timeGame" => $parameters['gamesParameters']['timeGame'],
            "timeDuel" => $parameters['gamesParameters']['timeDuel']
        ); 
               
        return $this->get("talker")->response($arr);
    }
    
    /**
    * Devulve los jugadores del equipo del usuario en session
    */
    public function teamUserSessionAction() {
    
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	//echo $user->getId();
    	$em = $this->getDoctrine()->getEntityManager();
     	
        if($user->getTeam()){
            $entities = $em->getRepository("LiderBundle:Player")->getArrayEntityWithOneLevel(
                         array("team"=>$user->getTeam()->getId(), "deleted" => false));
        
            return $this->get("talker")->response($entities);
        }else{
            return $this->get("talker")->response(array("total"=>0, "data"=>array()));
        }
    	
    }

    public function getRangePositionAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repo = $dm->getRepository("LiderBundle:QuestionHistory");
        $list = $repo->findRangePosition();
        $list = $list->toArray();
        return $this->get("talker")->response(array("total" => count($list), "data" => $list));
    }

    public function getGeneralStatisticsAction(){
        $dm = $this->get('doctrine_mongodb')->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $statistics = $dm->getRepository("LiderBundle:QuestionHistory")->getPlayerTotalReports($user);
        $statistics = $statistics->toArray();
        $count=0;
        $win=0; $lost=0;

        if($statistics){

            $obj = $statistics[0]; 
            $lost = $obj["lost"];
            $count = $obj["count"];
            $win = $obj["win"];
        }
        
        
        $eff = 0;
        if($count > 0)
            $eff = ($win * 100) / $count;
        $arr = array(
            'effectiveness' => $eff,
            'counteffectiveness' => $count,
            'wineffectiveness' => $win 
        );

        return $this->get("talker")->response($arr);
    }
    
    /**
     * Cambiar la foto de perfil del usuario
     */
    public function changePhotoAction() {
    	 
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	 
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$request = $this->get("request");
    
    	$uploadedFile = $request->files->get('imagen');
    	$className = self::$NAMESPACE.$this->getName();
    	 
    	$image = new Image();
    	$image->setName($uploadedFile->getClientOriginalName());
    	$image->setFile($uploadedFile->getPathname());
    	$image->setMimetype($uploadedFile->getClientMimeType());
    	$image->setEntity($className);
    	$image->setEntityId($user->getId());
    
    	$dm->persist($image);
    	$dm->flush();
    	 
    	$user->setImage($image->getId());
    	 
    	$em->flush();
    	 
    	return $this->get("talker")->response(array (
				"success" => true,
				"message" => $this->save_successful,
    			"image" => $image->getId()
		));
    }

    /**
     * Actualizar password del usuario
     */
    public function updatePasswordAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->get("request");
        $data = $request->getContent();

        $data = json_decode($data, true);

        $oldPassword = $data['oldPassword'];
        $newPassword = $data['newPassword'];
 
        $user = $this->container->get('security.context')->getToken()->getUser();
       
        $oldPassword = $this->generatePass($oldPassword);
        $newPassword = $this->generatePass($newPassword);

        if($oldPassword != $user->getPassword()){
            throw new \Exception("Wrong Password");            
        }

        $this->changePassword($newPassword);

        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }

    private function changePassword($password)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->container->get('security.context')->getToken()->getUser();
        $user->setPassword($password);

        $em->flush();
    }

    /**
     * Actualizar la contraseña en el login o reseteada por el administrador
     */
    public function resetPasswordAction()
    {      
        $em = $this->getDoctrine()->getEntityManager();  

        $request = $this->get("request");
        $data = $request->getContent();
        $data = json_decode($data, true);

        $password = $data['password'];

        $password = $this->generatePass($password);

        $this->changePassword($password);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $user->setChangePassword(false);

        $em->flush();

        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }
    
    /**
     * Estadisticas del usuario
     */
    public function getStatisticsAction($playerId=null){
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->get("request");
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	if($playerId){    		
    		$user = $em->getRepository("LiderBundle:Player")->find($playerId);
    		if(!$user)
    			throw new \Exception("User not found");
    	}
    	
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$statistics = $dm->getRepository("LiderBundle:QuestionHistory")->getPlayerReports($user);
    	$stArray = $statistics->toArray();
    	$categories = array();
    	foreach ($stArray as $obj){
    		
    		$wint = $obj["winTest"];
    		$lostt = $obj["lostTest"];
    		$countt = $obj["countTest"];
    		
    		$win = $obj["win"];
    		$lost = $obj["lost"];
    		$count = $obj["count"];
    		
    		if(!array_key_exists($obj["question.categoryName"], $categories)){
    			$categories[$obj["question.categoryName"]] = array();
    			$categories[$obj["question.categoryName"]]["practice"] = array();
    			$categories[$obj["question.categoryName"]]["tournament"] = array();
    		}
    		
    		$effT = 0;
    		if($countt > 0)
    			$effT = ($wint * 100) / $countt;
    		
    		$categories[$obj["question.categoryName"]]["practice"]["effectiveness"] = $effT;
    		$categories[$obj["question.categoryName"]]["practice"]["count"] = $countt;
    		$categories[$obj["question.categoryName"]]["practice"]["win"] = $wint;
    		$categories[$obj["question.categoryName"]]["practice"]["lost"] = $lostt;
    		
    		
    		$eff = 0;
    		if($count > 0)
    			$eff = ($win * 100) / $count;
    		
    		$categories[$obj["question.categoryName"]]["tournament"]["effectiveness"] = $eff;
    		$categories[$obj["question.categoryName"]]["tournament"]["count"] = $count;
    		$categories[$obj["question.categoryName"]]["tournament"]["win"] = $win;
    		$categories[$obj["question.categoryName"]]["tournament"]["lost"] = $lost;
    		
    	}
    	
    	return $this->get("talker")->response($categories);
    }
    
    public function setImageAction($id) {
        
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository("LiderBundle:Player")->findOneBy(array("id" => $id, "deleted" => false));
        if(!$entity)
            throw new \Exception("Entity no found");
        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $request = $this->get("request");

        $uploadedFile = $request->files->get('imagen');
        $className = self::$NAMESPACE.$this->getName();
        
        $image = new Image();
        $image->setName($uploadedFile->getClientOriginalName());
        $image->setFile($uploadedFile->getPathname());
        $image->setMimetype($uploadedFile->getClientMimeType());
        $image->setEntity($className);
        $image->setEntityId($id);
        
        $dm->persist($image);
        $dm->flush();
        
        $entity->setImage($image->getId());
        
        $em->flush();
        
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }    
}
