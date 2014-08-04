<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Answer class
 * @ORM\Table(name="answer")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\MainRepository")
 */
class Answer extends Entity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank()
	 */
	private $answer;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Question",cascade={"persist"})
	 * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $question;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $selected = true;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set answer
     *
     * @param string $answer
     * @return Answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     * @return Answer
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean 
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Set question
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Question $question
     * @return Answer
     */
    public function setQuestion(\Lider\Bundle\LiderBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
