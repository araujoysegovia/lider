<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Lider\Bundle\LiderBundle\Entity\Question as QuestionEntity;

/**
 * @MongoDB\EmbeddedDocument 
 */
class Question extends QuestionEntity
{
    /**
     * @MongoDB\Id  
     */
    private $id;
    
    /**
     * @MongoDB\Int
     */
    private  $questionId;

    /**
     * @MongoDB\String
     */
    private $question;
        
    /**
     * @MongoDB\Boolean
     */
    private $hasImage;

    /**
     * @MongoDB\String
     */
    private $image;    
   
    /**
     * @MongoDB\String
     */
    private $categoryName;

    

    public function getDataFromQuestionEntity(QuestionEntity $question)
    {
        $this->setQuestionId($question->getId());
        $this->setQuestion($question->getQuestion());
        $this->setHasImage($question->getHasImage());
        $this->setImage($question->getImage());
        $this->setCategoryName($question->getCategory()->getName());   
        //$this->setAnswers($question->getAnswers());

    }


    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set questionId
     *
     * @param int $questionId
     * @return self
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
        return $this;
    }

    /**
     * Get questionId
     *
     * @return int $questionId
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * Set question
     *
     * @param string $question
     * @return self
     */
    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }

    /**
     * Get question
     *
     * @return string $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set hasImage
     *
     * @param boolean $hasImage
     * @return self
     */
    public function setHasImage($hasImage)
    {
        $this->hasImage = $hasImage;
        return $this;
    }

    /**
     * Get hasImage
     *
     * @return boolean $hasImage
     */
    public function getHasImage()
    {
        return $this->hasImage;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set categoryName
     *
     * @param string $categoryName
     * @return self
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string $categoryName
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }
}
