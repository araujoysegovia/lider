<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Question class
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\MainRepository")
 */
class Question extends Entity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 */
	private $question;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $hasImage = false;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Category",cascade={"persist"})
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $category;
	
	/**
	 * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")
	 */
	private $answers;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $checked;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Player",cascade={"persist"})
	 * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
	 */
	private $user;

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
     * Set question
     *
     * @param string $question
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set hasImage
     *
     * @param boolean $hasImage
     * @return Question
     */
    public function setHasImage($hasImage)
    {
        $this->hasImage = $hasImage;

        return $this;
    }

    /**
     * Get hasImage
     *
     * @return boolean 
     */
    public function getHasImage()
    {
        return $this->hasImage;
    }

    /**
     * Set category
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Category $category
     * @return Question
     */
    public function setCategory(\Lider\Bundle\LiderBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add answers
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Answer $answers
     * @return Question
     */
    public function addAnswer(\Lider\Bundle\LiderBundle\Entity\Answer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Answer $answers
     */
    public function removeAnswer(\Lider\Bundle\LiderBundle\Entity\Answer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     * @return Question
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get checked
     *
     * @return boolean 
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set user
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $user
     * @return Question
     */
    public function setUser(\Lider\Bundle\LiderBundle\Entity\Player $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Player 
     */
    public function getUser()
    {
        return $this->user;
    }
}
