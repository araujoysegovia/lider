<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Group class
 * @ORM\Table(name="team_group")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\MainRepository")
 */
class Group extends Entity
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
	 * @ORM\ManyToOne(targetEntity="Tournament",cascade={"persist"})
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $tournament;
	
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
     * @return Group
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
     * Set active
     *
     * @param boolean $active
     * @return Group
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
     * Set tournament
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Tournament $tournament
     * @return Group
     */
    public function setTournament(\Lider\Bundle\LiderBundle\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Tournament 
     */
    public function getTournament()
    {
        return $this->tournament;
    }
}
