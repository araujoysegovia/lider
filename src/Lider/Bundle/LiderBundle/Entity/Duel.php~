<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Duel class
 * @ORM\Table(name="duel")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\DuelRepository")
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
	 * @ORM\Column(type="datetime", nullable=true)	 
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
	 * @ORM\ManyToOne(targetEntity="Player",cascade={"persist"})
	 * @ORM\JoinColumn(name="player_win_id", referencedColumnName="id")
	 */
	private $player_win;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Player",cascade={"persist"})
	 * @ORM\JoinColumn(name="player_lost_id", referencedColumnName="id")
	 */	
	private $player_lost;

    /**
     * @ORM\ManyToOne(targetEntity="Tournament",cascade={"persist"}, inversedBy="duels")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */    
    private $tournament;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished = false;

 

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
     * Set finished
     *
     * @param boolean $finished
     * @return Duel
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return boolean 
     */
    public function getFinished()
    {
        return $this->finished;
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

    /**
     * Set player_win
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $playerWin
     * @return Duel
     */
    public function setPlayerWin(\Lider\Bundle\LiderBundle\Entity\Player $playerWin = null)
    {
        $this->player_win = $playerWin;

        return $this;
    }

    /**
     * Get player_win
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Player 
     */
    public function getPlayerWin()
    {
        return $this->player_win;
    }

    /**
     * Set player_lost
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $playerLost
     * @return Duel
     */
    public function setPlayerLost(\Lider\Bundle\LiderBundle\Entity\Player $playerLost = null)
    {
        $this->player_lost = $playerLost;

        return $this;
    }

    /**
     * Get player_lost
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Player 
     */
    public function getPlayerLost()
    {
        return $this->player_lost;
    }

    /**
     * Set tournament
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Tournament $tournament
     * @return Duel
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
