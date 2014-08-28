<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lider\Bundle\LiderBundle\Document\QuestionHistory;
use Lider\Bundle\LiderBundle\Document\Image;

class QuestionController extends Controller
{
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
    	
    	$dm = $this->get('doctrine_mongodb')->getManager();
    	    	
    	$question = $this->get("question_manager")->generateQuestions(1);
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	
    	$questionHistory = new QuestionHistory();
    	$questionHistory->setPlayerId($user->getId());
    	$questionHistory->setQuestionId($question[0]['id']);
    	//$questionHistory->setAnswerId();
    	$questionHistory->setDuel(false);
    	//$questionHistory->setDuelId();
    	$questionHistory->setEntryDate(new \MongoDate());
    	
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
     * @param unknown $idQuestion
     * @param unknown $idAnswer
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
    	//print_r($user);
    	//print_r($this->container->get('security.context')->getToken());
    	if($user->getId() != $entity->getPlayerId()){
    		throw new \Exception("Question no found");
    	}
    	
    	$question = $em->getRepository("LiderBundle:Question")->findOneBy(array("id" =>$questionId, "deleted" => false));
    	if(empty($question))
    		throw new \Exception("No entity found");  

    	$res = array();
    	$res['success'] = false;
    	if($answerId != "no-answer"){
    		foreach ($question->getAnswers()->toArray() as $value) {
    			if($value->getSelected()) {
    				$entity->setAnswerOk($value->getId());
    				if($answerId == $value->getId()){
    					$res['success'] = true;
    					break;
    				}    				
    			}
    		}
    	}

    	
    	
    	
    	if(!$entity){
    		throw new \Exception("Entity not found");
    	}
    	
    	$entity->setFinished(true);
     	$entity->setSelectedAnswer($answerId);     	
     	
     	
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

}
