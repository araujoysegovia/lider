<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Lider\Bundle\LiderBundle\Entity\Answer as AnswerEntity;

/**
 * @MongoDB\EmbeddedDocument 
 */
class Answer extends AnswerEntity
{
    /**
     * @MongoDB\Id  
     */
    private $id;

    /**
     * @MongoDB\Int
     */
    private $answerId;

    /**
     * @MongoDB\String  
     */
    private $answer;

	/**
     * @MongoDB\Boolean  
     */
    private $selected;  

	/**
     * @MongoDB\Boolean  
     */
    private $help;  



    public function getDataFromAnswerEntity(AnswerEntity $answer)
    {
        $this->setAnswerId($answer->getId());
        $this->setAnswer($answer->getAnswer());
        $this->setSelected($answer->getSelected());
        $this->setHelp($answer->getHelp());        
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
     * Set answerId
     *
     * @param int $answerId
     * @return self
     */
    public function setAnswerId($answerId)
    {
        $this->answerId = $answerId;
        return $this;
    }

    /**
     * Get answerId
     *
     * @return int $answerId
     */
    public function getAnswerId()
    {
        return $this->answerId;
    }

    /**
     * Set answer
     *
     * @param string $answer
     * @return self
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * Get answer
     *
     * @return string $answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     * @return self
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean $selected
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Set help
     *
     * @param boolean $help
     * @return self
     */
    public function setHelp($help)
    {
        $this->help = $help;
        return $this;
    }

    /**
     * Get help
     *
     * @return boolean $help
     */
    public function getHelp()
    {
        return $this->help;
    }
}
