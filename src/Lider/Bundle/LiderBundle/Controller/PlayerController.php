<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lider\Bundle\LiderBundle\Document\Image;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Lider\Bundle\LiderBundle\Document\Suggestion;
use Lider\Bundle\LiderBundle\Entity\Player;
use Lider\Bundle\LiderBundle\Entity\PlayerPoint;
use Lider\Bundle\LiderBundle\Entity\Tournament;

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

    	$em = $this->getDoctrine()->get0Manager();
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	
    	$atoken = $request->get("access_token");
    	$code = $request->get("code");
    	if(!$atoken || !$code)
    		throw new \Exception("Token not found");
		
    	//echo "token: ".$atoken."<br/>";
    	$content = $this->getUserInfo($atoken);
    	$data = json_decode($content, true);
    	//echo "con google";
    	//print_r($data);
    	if(!is_array($data) || (is_array($data) && !array_key_exists("email", $data)))
    		throw new \Exception("User not found");
    	
    	$repo = $em->getRepository("LiderBundle:Player");
    	$userName = $data["email"];
    	$user = $repo->findOneByEmail($userName);
    	
    	if(!$user)
    		throw new \Exception("User Not Registered", 403);
    	
    	$arr = $this->login($user);

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
        $session = null;
        if($sess)
        {
            $session = $sess;
        }
        $arr = $this->login($user, $session);

        return $this->get("talker")->response($arr);
    }

    private function login($user, $session = null)
    {
        $em = $this->getDoctrine()->getManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $request = $this->get("request");
        if(is_null($session))
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
        
        $arr['user'] = array(
            'id' => $user->getId(),
            "email" => $user->getEmail(),
            "name" => $user->getName(),
            "latname" => $user->getLastname(),
            "image" => $user->getImage(),
            "office" => $office,
            "roles" => $roles,
            "team" => array(),
            "changePassword" => $user->getChangePassword(),
        );
        
        $team = $user->getTeam();
        if($team){
            $arr['user']['team'] = array(
                "id" => $user->getTeam()->getId(),
                "name" => $user->getTeam()->getName()               
            ) ;
        }

        // $playerGameInfo = $repo->getPlayerGamesInfo($user->getId());
        // $arr['user']['gameInfo'] = $playerGameInfo;
        $parameters = $this->get('parameters_manager')->getParameters();

        
        $repoParameters = $em->getRepository("LiderBundle:Parameters");
        
        $ptimeQuestionPractice = $repoParameters->findOneBy(array('name'=>'timeQuestionPractice'));
        $ptimeQuestionDuel = $repoParameters->findOneBy(array('name'=>'timeQuestionDuel'));
        $ptimeGame = $repoParameters->findOneBy(array('name'=>'timeGame'));
        $ptimeDuel = $repoParameters->findOneBy(array('name'=>'timeDuel'));
        //echo "\n".$ptimeQuestionPractice->getValue();
        
        $arr['config'] = array(
        		"timeQuestionPractice" => $ptimeQuestionPractice->getValue(),
        		"timeQuestionDuel" => $ptimeQuestionDuel->getValue(),
        		"timeGame" => $ptimeGame->getValue(),
        		"timeDuel" => $ptimeDuel->getValue()
        );
        
//         $arr['config'] = array(
//             "timeQuestionPractice" => $parameters['gamesParameters']['timeQuestionPractice'] ,
//             "timeQuestionDuel" => $parameters['gamesParameters']['timeQuestionDuel'],
//             "timeGame" => $parameters['gamesParameters']['timeGame'],
//             "timeDuel" => $parameters['gamesParameters']['timeDuel']
//         ); 

        

        return $arr;
    }

    public function getGameInfoAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $wonGames = 0;
        if($user->getWonGames()){
            $wonGames = $user->getWonGames();
        }

        $lostGames = 0;
        if($user->getLostGames()){
            $lostGames = $user->getLostGames();
        }

        $return = array(
            'win' => $wonGames,
            'lost' => $lostGames,
            'points' => 0,
        );
    
        $points = $user->getPlayerPoints()->toArray();
        foreach ($points as $value) {
            if($value->getTournament()->getActive()){                
                $return['points'] += $value->getPoints();
            }
        }
        return $this->get("talker")->response($return);
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

    public function getRangePositionAction($tournamentId = null)
    {        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repo = $dm->getRepository("LiderBundle:QuestionHistory");
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if($user->getTeam()){
        	$tournamentId = $user->getTeam()->getTournament()->getId();
        }        

        $list = $repo->findRangePosition(intval($tournamentId));
        $list = $list->toArray();
        $orderBy = function($data, $field){
            $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
            usort($data, create_function('$a, $b', $code));
            return $data;
        };
        $slist = $orderBy($list, "totalPoint");
        $list=array();
        foreach ($slist as $value) {
            array_unshift($list, $value);
        }

        $newList = [];
        foreach ($list as $key => $value) {                        
            if($value['total'] > 0){
                $percentageCorrect = ($value['win'] / $value['total']) * 100;
                $percentageIncorrect = ($value['lost'] / $value['total']) * 100;
                $percentageCorrect = round($percentageCorrect);
                $percentageIncorrect = round($percentageIncorrect);
                $value['percentageCorrect'] = $percentageCorrect;
                $value['percentageIncorrect'] = $percentageIncorrect;
            }            
            $newList[] = $value;
        }
        
        return $this->get("talker")->response(array("total" => count($list), "data" => $newList));
    }

    public function getRangePositionByPracticeAction($tournamentId = null)
    {        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repo = $dm->getRepository("LiderBundle:QuestionHistory");
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getEntityManager();
        $repoTournament = $em->getRepository("LiderBundle:Tournament");
        if($user->getTeam()){
            $tournamentId = $user->getTeam()->getTournament()->getId();
        } 

        
        
        //$questionHistoryList = $repo->findAll();
         
        //echo count($questionHistoryList);
         
//         foreach ($questionHistoryList as $key => $qh) {
//         	$qh->setTournamentid('1');
//         	$dm->flush();
//         }
        
        
//         $questionHistory->addAnswer($ansD);
        

        $list = $repo->findRangePositionByPractice(intval($tournamentId));
        $list = $list->toArray();
        $orderBy = function($data, $field){
            $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
            usort($data, create_function('$a, $b', $code));
            return $data;
        };
        $slist = $orderBy($list, "totalPoint");
        $list=array();
        foreach ($slist as $value) {
            array_unshift($list, $value);
        }

        $newList = [];
        foreach ($list as $key => $value) {                        
            if($value['total'] > 0){
                $percentageCorrect = ($value['win'] / $value['total']) * 100;
                $percentageIncorrect = ($value['lost'] / $value['total']) * 100;
                $percentageCorrect = round($percentageCorrect);
                $percentageIncorrect = round($percentageIncorrect);
                $value['percentageCorrect'] = $percentageCorrect;
                $value['percentageIncorrect'] = $percentageIncorrect;
                
//                 $numeroTorneo = intval($value['tournamentId']);
//                 echo "\n".$numeroTorneo."\n";
                
//                 echo "\n".$value['tournamentId']."\n";
//                  $t = $repoTournament->find($numeroTorneo);
//                  if($t){
//                  	$value['tournament'] = $t->getName();
//                  }else{
//                  	$value['tournament'] = 'Sin torneo';
//                  }
                
            }            
            $newList[] = $value;
        }

        return $this->get("talker")->response(array("total" => count($list), "data" => $newList));
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

    /**
     * Guardar una sugerencia
     */
    public function saveSuggestionAction() 
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $gearman = $this->get("gearman");
        $request = $this->get("request");
        $data = $request->getContent();
         
        if(empty($data) || !$data)
            throw new \Exception("No data");
         
        $data = json_decode($data, true);
         
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $subject = $data['subject'];
        $text = $data['text'];
        
        $playerD = new \Lider\Bundle\LiderBundle\Document\Player();
        $playerD->getDataFromPlayerEntity($user);

        $suggestion = new Suggestion();
        $suggestion->setSubject($subject);
        $suggestion->setPlayer($playerD);
        $suggestion->setText($text);
        $suggestion->setSuggestionDate(new \MongoDate());
        
        $dm->persist($suggestion);
        $dm->flush();
       // try{
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotification', json_encode(array(
            'subject' => 'Nueva Sugerencia Registrada',
            'templateData' => array(
                'title' => 'Nueva Sugerencia',
                'user' => array(
                    'image' => $user->getImage(),
                    'name' => $user->getName(),
                    'lastname' => $user->getLastname()
                ),
                'subjectUser' => $subject,
                'body' => '<p>'.$text.'</p>'
            )
            
        )));
//         }catch(\Exception $e){
//         	$salida = shell_exec('nmap 10.102.1.21');
//         	echo "<pre>$salida</pre>";
//         	print_r($e->getTraceAsString());
//         }
        
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

    /**
     * Actualizar jugador
     */
    public function updateAction($id = null) {
        
        //$ec = $this->getRequestEntity($id);     

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->get("request");
        $data = $request->getContent();
         
        if(empty($data) || !$data)
            throw new \Exception("No data");
         
        $data = json_decode($data, true);
        
        $player = $em->getRepository("LiderBundle:Player")->findOneBy(array("id" => $data['id'], "deleted" => false));
        if(!$player)
            throw new \Exception("Player no found");
        

        $office = $em->getRepository("LiderBundle:Office")->findOneBy(array("id" => $data['office']['id'], "deleted" => false));
        if(!$office)
            throw new \Exception("Office no found");

        $role = $em->getRepository("LiderBundle:Role")->findOneBy(array("id" => $data['roles'][0]['id'], "deleted" => false));
        if(!$role)
            throw new \Exception("Role no found");

        if($data['team']['id']){
            $team = $em->getRepository("LiderBundle:Team")->findOneBy(array("id" => $data['team']['id'], "deleted" => false));
            if(!$team)
                throw new \Exception("Team no found");           

            $player->setTeam($team);
        }
        
        
        $player->setName($data['name']);
        $player->setLastname($data['lastname']);
        $player->setEmail($data['email']);
        $player->setOffice($office);

        $roles = $player->getRoles();        
        foreach ($roles as $key => $value) {
            $player->removeRole($value);
        }
        
        $player->addRole($role);        
        $player->setActive($data['active']);

        $em->flush();

        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }


    public function getPlayerAnalysisAction($tournamentId)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();

        if(!$tournamentId){
            return $this->get("talker")->response(array('total' => 0, 'data' => array())); 
        }

        $reportQuestions = $dm->getRepository('LiderBundle:QuestionHistory')->findRangePosition($tournamentId);
        
        $rq = $reportQuestions->toArray();

        return $this->get("talker")->response(array('total' => count($rq), 'data' => $rq)); 

    }

    public function notificationAllAction()
    {
        $gearman = $this->get('gearman');
      
        $request = $this->get("request");
     
        $tournament = $request->get('tournamentId');
        $subject = $request->get("subject");
        $message = $request->get("message");

        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmailToPlayersFromTournament', json_encode(array(
                'tournament' => $tournament,
                "subject" => $subject,
                "content" => array(
                    "title" => "Notificacion del administrador",
                    "subjectMessage" => $subject,
                    "body" => $message
                )
            )));
   
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

    public function notificationPlayerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repoPlayer = $em->getRepository("LiderBundle:Player");
        $gearman = $this->get('gearman');
      
        $request = $this->get("request");
     
        $playerId = $request->get('player');
        $subject = $request->get("subject");
        $message = $request->get("message");
        $template = "LiderBundle:Templates:emailnotification.html.twig";
        $player = $repoPlayer->find($playerId);

        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', json_encode(array(
                "subject" => $subject,
                "to" => $player->getEmail(),
                "viewName" => $template,
                "content" => array(
                    "title" => "Notificacion del administrador",
                    "subjectMessage" => $subject,
                    "body" => $message
                )
            )));
   
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

    public function notificationDuelAction()
    {
       
        $gearman = $this->get('gearman');
      
        $request = $this->get("request");
     
        $tournament = $request->get('tournamentId');
    
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendNotificationDuel', json_encode(array(
                'tournament' => $tournament,
            )));
   
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

    /**
     * Resetear password del jugador desde el administrador
     */
    public function passwordResetAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $player = $em->getRepository("LiderBundle:Player")->find($id);

        if(!$player)
           throw new \Exception("Player no found");

        $password = $this->generatePass('araujo123');

        $player->setPassword($password);
        $player->setChangePassword(true);

        $em->flush();
        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    }
    
    public function reportErrorAction()
    {
    	$gearman = $this->get('gearman');
    	
    	$request = $this->get("request");
    	 
    	$data = $request->getContent();
    	if(empty($data) || !$data)
            throw new \Exception("No data");
         
        $data = json_decode($data, true);
    	
//     	$result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotification', json_encode(array(
//     			'subject' => "Error de aplicacion",
//     			'templateData' => array(
// 	    			'title' => 'Error en la aplicacion',
//     				'subjectUser' => $data['title'],
//     				'body' => $data['content']
//     			)
//     	)));
    	 
    	return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }

    public function updatePointsPlayerByDuelAction($playerId, $duelId)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $em = $this->getDoctrine()->getEntityManager();
        $questionHistoryRepo = $dm->getRepository('LiderBundle:QuestionHistory');
        $listQuestion = $questionHistoryRepo->findBy(array("player.playerId" => intval($playerId), "duelId" => intval($duelId)));
        $player = $em->getRepository('LiderBundle:Player')->find($playerId);
        if($player)
        {
            $duel = $em->getRepository('LiderBundle:Duel')->find($duelId);
            if($duel)
            {
                if($duel->getPlayerOne()->getId() == $player->getId())
                {
                    $duel->setPointOne(0);
                }
                elseif($duel->getPlayerTwo()->getId() == $player->getId()){
                    $duel->setPointTwo(0);
                }
                else{
                    throw new \Exception("El jugador no pertenece a este duelo");
                }
                $em->flush();
                foreach($listQuestion as $question)
                {
                    if($question->getFind())
                    {
                        $pp = $em->getRepository('LiderBundle:PlayerPoint')->findOneBy(array("player" => $player, "duel" => $duel, "question" => $question->getQuestion()->getQuestionId()));
                        $player->setWonGames($player->getWonGames() + 1);
                        if(!$pp)
                        {
                            $q = $em->getRepository('LiderBundle:Question')->find($question->getQuestion()->getQuestionId());
                            $playerPoint = new PlayerPoint();
                            $playerPoint->setPlayer($player);
                            $playerPoint->setPoints($question->getPoints());
                            $playerPoint->setDuel($duel);
                            $playerPoint->setTournament($duel->getTournament());
                            $playerPoint->setTeam($player->getTeam());
                            $playerPoint->setQuestion($q);
                            $date = $question->getEntryDate()->format('Y-m-d H:i:s');
                            $playerPoint->setEntrydate(new \Datetime($date));
                            $em->persist($playerPoint);
                        }
                        if($duel->getPlayerOne()->getId() == $player->getId())
                        {
                            $duel->setPointOne($duel->getPointOne() + $question->getPoints());
                        }
                        elseif($duel->getPlayerTwo()->getId() == $player->getId()){
                            $duel->setPointTwo($duel->getPointTwo() + $question->getPoints());
                        }
                    }
                    else{
                        $player->setLostGames($player->getLostGames() + 1);
                    }
                }
                $em->flush();
            }
        }
        return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
        
    }

    /**
     * Obtener todas las preguntas realizadas por un juegador tanto en duelos como en practicas
     */
    public function questionsForPlayerAction($playerId)
    {

        $dm = $this->get('doctrine_mongodb')->getManager();

        $repo = $dm->getRepository('LiderBundle:QuestionHistory');

        $questions =  $repo->questionsForPlayer();

      
        $qs = [];
        $array = [];
        foreach ($questions as $key => $value) {
           // print_r($value->getQuestion());
            $array[] = $this->normalizer($value, $this->documentNameSpace."QuestionHistory");
            // $qs[] = $value->getQuestion();
            // print_r($qs);
        }

        //print_r($array);


        //print_r($questions);

        return $this->get("talker")->response(array('total' => count($qs), 'data' => $qs)); 
    }
    
    public function setTournamentToQuestionHistory($tournamentId = null)
    {
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$repo = $dm->getRepository("LiderBundle:QuestionHistory");
    
    	$questionHistoryList = $repo->findAll();
    	
    	echo count($questionHistoryList);
    	
    	return $this->get("talker")->response(array("total" => count($list), "data" => $newList));
    }

}
