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
	 * @ORM\Column(type="text", length=100)
	 * @Assert\NotBlank()
	 * @Assert\Length(max=100)
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
}
