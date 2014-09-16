<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tournament class
 * @ORM\Table(name="tournament")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\MainRepository")
 */
class Tournament extends Entity
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
	 * @ORM\Column(type="date")
	 * @Assert\NotBlank()
	 */
	private $startdate;
	
	/**
	 * @ORM\Column(type="date")
	 * @Assert\NotBlank()
	 */
	private $enddate;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active = true;
     
    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="tournament")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="tournament")
     */
    private $groups;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * @return Tournament
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
     * Set startdate
     *
     * @param \DateTime $startdate
     * @return Tournament
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime 
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     * @return Tournament
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime 
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Tournament
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
     * Add teams
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Team $teams
     * @return Tournament
     */
    public function addTeam(\Lider\Bundle\LiderBundle\Entity\Team $teams)
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Team $teams
     */
    public function removeTeam(\Lider\Bundle\LiderBundle\Entity\Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Add groups
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Group $groups
     * @return Tournament
     */
    public function addGroup(\Lider\Bundle\LiderBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Group $groups
     */
    public function removeGroup(\Lider\Bundle\LiderBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
