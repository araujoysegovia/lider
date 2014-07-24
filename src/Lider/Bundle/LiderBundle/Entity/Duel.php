<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Duel class
 * @ORM\Table(name="duel")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\MainRepository")
 */
class Duel extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
	 * @ORM\ManyToOne(targetEntity="Group",cascade={"persist"})
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $group;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Game",cascade={"persist"})
	 * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $game;
	
	/**
	 * @ORM\Column(type="datetime")
	 * @Assert\NotBlank()
	 */
	private $startdate;
	
	/**
	 * @ORM\Column(type="datetime")
	 * @Assert\NotBlank()
	 */
	private $enddate;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Player",cascade={"persist"})
	 * @ORM\JoinColumn(name="playerone_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $player_one;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Player",cascade={"persist"})
	 * @ORM\JoinColumn(name="playertwo_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $player_two;
	
	/**
	 * @ORM\Column(type="integer")
	 * @Assert\NotBlank()
	 */
	private $point_one = 0;
	
	/**
	 * @ORM\Column(type="integer")
	 * @Assert\NotBlank()
	 */
	private $point_two = 0;
	
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
     * Set startdate
     *
     * @param \DateTime $startdate
     * @return Duel
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
     * @return Duel
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
     * Set point_one
     *
     * @param integer $pointOne
     * @return Duel
     */
    public function setPointOne($pointOne)
    {
        $this->point_one = $pointOne;

        return $this;
    }

    /**
     * Get point_one
     *
     * @return integer 
     */
    public function getPointOne()
    {
        return $this->point_one;
    }

    /**
     * Set point_two
     *
     * @param integer $pointTwo
     * @return Duel
     */
    public function setPointTwo($pointTwo)
    {
        $this->point_two = $pointTwo;

        return $this;
    }

    /**
     * Get point_two
     *
     * @return integer 
     */
    public function getPointTwo()
    {
        return $this->point_two;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Duel
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
     * @return Duel
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

    /**
     * Set game
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Game $game
     * @return Duel
     */
    public function setGame(\Lider\Bundle\LiderBundle\Entity\Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set player_one
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $playerOne
     * @return Duel
     */
    public function setPlayerOne(\Lider\Bundle\LiderBundle\Entity\Player $playerOne = null)
    {
        $this->player_one = $playerOne;

        return $this;
    }

    /**
     * Get player_one
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Player 
     */
    public function getPlayerOne()
    {
        return $this->player_one;
    }

    /**
     * Set player_two
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $playerTwo
     * @return Duel
     */
    public function setPlayerTwo(\Lider\Bundle\LiderBundle\Entity\Player $playerTwo = null)
    {
        $this->player_two = $playerTwo;

        return $this;
    }

    /**
     * Get player_two
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Player 
     */
    public function getPlayerTwo()
    {
        return $this->player_two;
    }
}
