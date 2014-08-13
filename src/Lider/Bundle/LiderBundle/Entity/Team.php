<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Team class
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\MainRepository")
 */
class Team extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank()
     * @Assert\Length(max=100)
	 */
	private $name;
	
	
	/**
	 * @ORM\Column(type="string", nullable = true)	 
	 */
	private $image;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Group",cascade={"persist"})
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $group;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active = true;


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
     * Set name
     *
     * @param string $name
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Team
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Team
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set group
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Group $group
     * @return Team
     */
    public function setGroup(\Lider\Bundle\LiderBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
    }
}
