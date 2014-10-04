<?php
namespace Lider\Bundle\LiderBundle\Controller;

use Lider\Bundle\LiderBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lider\Bundle\LiderBundle\Document\QuestionHistory;
use Lider\Bundle\LiderBundle\Document\Image;
use Lider\Bundle\LiderBundle\Document\ReportQuestion;

class ReportQuestionController extends Controller
{
    private $maxSec = 30;

    public function getName(){
    	return "ReportQuestion";
    }

	public function getReportQuestionsAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();

        $reportQuestions = $dm->getRepository('LiderBundle:ReportQuestion')->findBy(array("solved" => false));

        //print_r($reportQuestions);
        if($reportQuestions){
           
            // $arr = $reportQuestions->toArray();
            $array = array();
            foreach ($reportQuestions as $value) {
               $array[] = $this->normalizer($value, $this->documentNameSpace."ReportQuestion");
            }

            // print_r($arr);
            return $this->get("talker")->response(array("total" => count($reportQuestions), "data" => $array));

            //return $this->get("talker")->response($reportQuestions);                       
        }

        return $this->get("talker")->response(array("total" => 0, "data" => array())); 
        //echo $reportQuestions;
           
    }   

    public function reportSolveAction()
     {
     	$dm = $this->get('doctrine_mongodb')->getManager();
     	$request = $this->get("request");

     	$id = $request->get("id");
        $data = $request->getContent();
        
        if(empty($data) || !$data)
            throw new \Exception("No data");
        
        $data = json_decode($data, true);        

     	//$reportQuestion = $dm->getRepository('LiderBundle:ReportQuestion')->findBy(array("id" => $id));
     	$reportQuestion = $dm->getRepository('LiderBundle:ReportQuestion')->find($id);



		if(!$reportQuestion)
            throw new \Exception("Report no found");    

        $user = $this->container->get('security.context')->getToken()->getUser();
        $player = $reportQuestion->getPlayer();

     	$reportQuestion->setSolved(true);
        if($data['descriptionSolve']){
            $reportQuestion->setDescription($data['descriptionSolve']);
        }

     	$dm->flush();


        $gearman = $this->get("gearman");  
        $em = $this->getDoctrine()->getEntityManager();
        $notificationService = $this->get("notificationService");       


        $viewName = "LiderBundle:Templates:emailnotification.html.twig";

        $result = $gearman->doBackgroundJob('LiderBundleLiderBundleWorkernotification~sendEmail', 
            json_encode(array(
            "subject" => 'Lider - Pregunta reportada',
            "from" => $user->getEmail(),
            "to" => $player->getEmail(),
            "viewName" => $viewName,
            "content" => array(
                "title" => 'Lider - Pregunta reportada',
                "subjectMessage" => "",
                "body"=> $reportQuestion->getDescription()
            )
        )));


     	return $this->get("talker")->response($this->getAnswer(true, $this->update_successful));
     } 
}