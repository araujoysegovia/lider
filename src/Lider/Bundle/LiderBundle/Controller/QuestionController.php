<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lider\Bundle\LiderBundle\Document\QuestionHistory;
use Lider\Bundle\LiderBundle\Document\Image;
use Lider\Bundle\LiderBundle\Document\ReportQuestion;

class QuestionController extends Controller
{
    private $maxSec = 30;

    public function getName(){
    	return "Question";
    }
    
    /**
     * Verificar pregunta (Administrador)
     * @param $id
     * @throws \Exception
     */
    public function checkAction($id){
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $id, "deleted" => false));
    	if(!$entity)
    		throw new \Exception("Entity no found");
    	
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$entity->setUser($user);
    	$entity->setChecked(true);
    	
    	$em->flush();
    	
    	return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
    	
    }
    
    /**
     * Obtener pregunta de practica
     */
    public function getTestQuestionAction() {
    	
        $em = $this->getDoctrine()->getEntityManager();
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	    	
    	$question = $this->get("question_manager")->generateQuestions(1);
        //echo $question;
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	
        $playerD = new \Lider\Bundle\LiderBundle\Document\Player();
        $playerD->getDataFromPlayerEntity($user);

        $q = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $question[0]['id'], "deleted" => false));
        if(!$q)
            throw new \Exception("Entity no found");

        $questionD = new \Lider\Bundle\LiderBundle\Document\Question();
        $questionD->getDataFromQuestionEntity($q);
        $questionHistory = new QuestionHistory();
        $questionHistory->setPlayer($playerD);    
        $questionHistory->setQuestion($questionD);
        $questionHistory->setDuel(false);
      
        $questionHistory->setEntryDate(new \MongoDate());

         foreach ($q->getAnswers()->toArray() as $key => $value) {
      
            $ansD = new \Lider\Bundle\LiderBundle\Document\Answer();
            $ansD->getDataFromAnswerEntity($value);

            $questionHistory->addAnswer($ansD);
         }
      
    	
    	$dm->persist($questionHistory);
    	$dm->flush();
    	
    	$array = array(
    		'token' => $questionHistory->getId(),
    		'question' => $question
    	);
    	
    	return $this->get("talker")->response($array);
    }
    
    /**	
     * Verificar la respuesta enviada
     */
    public function checkAnswerAction() {
    	
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->get("request");
    	$data = $request->getContent();
    	
    	if(empty($data) || !$data)
    		throw new \Exception("No data");
    	
    	$data = json_decode($data, true);
    	
    	$questionId = $data['questionId'];
    	$answerId = $data['answerId'];
    	$token = $data['token'];
    	
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	$entity = $dm->getRepository("LiderBundle:QuestionHistory")
    				 ->findOneBy(array("id" => $token));
    	
    	if(!$entity){
    		throw new \Exception("Question no found");
    	}
        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user->getId() != $entity->getPlayer()->getPlayerId()){
            throw new \Exception("Question no found");
        }

        $now = new \DateTime();
        $diffTime = $now->format('U') - $entity->getEntryDate()->format('U');
        
        $parameters = $this->get('parameters_manager')->getParameters();

        $question = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" =>$questionId, "deleted" => false));
        if(empty($question))
            throw new \Exception("No entity found");  

        $isOk = false;

        foreach ($question->getAnswers()->toArray() as $value) {
            if($value->getSelected()) {
                $answerD = new \Lider\Bundle\LiderBundle\Document\Answer();
                $answerD->getDataFromAnswerEntity($value);                  
                $entity->setAnswerOk($answerD);
                if($answerId == $value->getId()){
                    $isOk = true;
                    break;
                }                   
            }
        }        

        if($diffTime >= $this->maxSec || $questionId=="no-answer"){
            $res = array();
            $res['success'] = false;
            $res['code'] = '01';  /*Tiempo agotado*/
            // if($parameters['gamesParameters']['answerShowPractice']){
            //     $res['answerOk'] = $entity->getAnswerOk()->getAnswerId();
            // }
            $entity->setTimeOut(true);
        }else{
    	
        	if($isOk){
                $res['success'] = true;
                $res['code'] = '00';
                $entity->setFind(true);
            }else{
            	$res = array();
            	$res['success'] = false;
                $res['code'] = '02';   /*Respuesta errada*/

                if($parameters['gamesParameters']['answerShowPractice'] == 'true'){
                    $res['answerOk'] = $entity->getAnswerOk()->getAnswerId();
                }
            }
    		

            $answerSelected = $em->getRepository("LiderBundle:Answer")->findOneBy(array("id" =>$answerId, "deleted" => false));
            if(empty($answerSelected))
                throw new \Exception("No entity found");  

            $answerSelectedD = new \Lider\Bundle\LiderBundle\Document\Answer();
                        $answerSelectedD->getDataFromAnswerEntity($answerSelected);
               
            $entity->setSelectedAnswer($answerSelectedD);      	
        	
    	}
        	    
    	$entity->setFinished(true);
     	$dm->flush();
     	
    	return $this->get("talker")->response($res);
    	
    }
    
    /**	
     * Setear una imagen a una pregunta y guardarla en la BD mongo
     * @param unknown $id
     * @throws \Exception
     */
    public function setImageAction($id) {

    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $id, "deleted" => false));
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
    	$entity->setHasImage(true);
    	
    	$em->flush();
    	 
    	return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }    
    
    /**	
     * Remover la imagen asignada a una pregunta
     * @param $id
     * @throws \Exception
     */
    public function removeImageAction($id) {
        	
    	$em = $this->getDoctrine()->getEntityManager();
    	$dm = $this->get('doctrine_mongodb')->getManager();
		
    	$question = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $id, "deleted" => false));
    	if(!$question)
    		throw new \Exception("Entity no found");

    	$image = $dm->getRepository("LiderBundle:Image")->findOneBy(array("id" => $question->getImage(), "deleted" => false));
    	if(!$image)
    		throw new \Exception("Document no found");
    	 
    	$dm->remove($image);    	
    	
    	$question->setImage(null);
    	$question->setHasImage(false);
    	    	
    	$em->flush();
    	$dm->flush();
    	
    	return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }    

    /**
     * Reportar un error en la pregunta
     */
    public function questionReportAction() {
        
        $em = $this->getDoctrine()->getEntityManager();
        $dm = $this->get('doctrine_mongodb')->getManager();
        $gearman = $this->get('gearman');
        
        $request = $this->get("request");
        $data = $request->getContent();
         
        if(empty($data) || !$data)
            throw new \Exception("No data");
         
        $data = json_decode($data, true);
         
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $questionId = $data['questionId'];
        $playerId = $user->getId();
        $reportText = $data['reportText'];        
        $causal = $data['causal']; 
        
        $playerD = new \Lider\Bundle\LiderBundle\Document\Player();
        $playerD->getDataFromPlayerEntity($user);
        
        $q = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" => $questionId, "deleted" => false));
        if(!$q)
            throw new \Exception("Entity no found");

        $questionD = new \Lider\Bundle\LiderBundle\Document\Question();
        $questionD->getDataFromQuestionEntity($q);

        $reportQuestion = new ReportQuestion();
        $reportQuestion->setQuestion($questionD);
        $reportQuestion->setPlayer($playerD);
        $reportQuestion->setReportText($reportText);
        $reportQuestion->setReportDate(new \MongoDate());
        $reportQuestion->setCausal($causal);
        
        $dm->persist($reportQuestion);
        $dm->flush();
        $body = '<p>'.$questionD->getQuestionId().' - '.$questionD->getQuestion().'</p><ul>';
        foreach($q->getAnswers()->toArray() as $value){
            $body .= '<li>'.$value->getAnswer().'</li>';
        }
        $body .= '</ul>';
        
        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~adminNotification', json_encode(array(
            'subject' => 'Nuevo reporte de pregunta',
            'templateData' => array(
                'title' => 'Nuevo Reporte',
                'user' => array(
                    'image' => $user->getImage(),
                    'name' => $user->getName(),
                    'lastname' => $user->getLastname()
                ),
                'subjectUser' => $reportText,
                'body' => $body
            )
            
        )));
        
        return $this->get("talker")->response($this->getAnswer(true, $this->save_successful));
    }
    
}
