<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tournament class
 * @ORM\Table(name="tournament")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\TournamentRepository")
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
     
    // /**
    //  * @ORM\OneToMany(targetEntity="Team", mappedBy="tournament")
    //  */
    // private $teams;

    // /**
    //  * @ORM\OneToMany(targetEntity="Group", mappedBy="tournament")
    //  */
    // private $groups;    

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min=1, max=5)
     */
    private $level;

    // /**
    //  * @ORM\OneToMany(targetEntity="Game", mappedBy="tournament")
    //  */
    // private $games;

    // /**
    //  * @ORM\OneToMany(targetEntity="Duel", mappedBy="tournament")
    //  */
    // private $duels;

    /**
     * Constructor
     */
    public function __construct()
    {
        // $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set level
     *
     * @param integer $level
     * @return Tournament
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }
}