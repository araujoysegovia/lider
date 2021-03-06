<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlayerPoint class
 * @ORM\Table(name="player_point")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\PlayerRepository")
 */
class PlayerPoint extends Entity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
	 * @ORM\Column(type="integer", length=100)
	 * @Assert\NotBlank()
	 */
	private $points;	

	/**
	 * @ORM\ManyToOne(targetEntity="Tournament",cascade={"persist"})
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 */
	private $tournament;	

	/**
	 * @ORM\ManyToOne(targetEntity="Team",cascade={"persist"})
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
	 */
	private $team;	

	/**
	 * @ORM\ManyToOne(targetEntity="Player",cascade={"persist"}, inversedBy="playerPoints")
	 * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
	 */
	private $player;

    /**
     * @ORM\ManyToOne(targetEntity="Duel",cascade={"persist"})
     * @ORM\JoinColumn(name="duel_id", referencedColumnName="id")
     */
    private $duel;

    /**
     * @ORM\ManyToOne(targetEntity="Question",cascade={"persist"})
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;
   

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
     * Set points
     *
     * @param integer $points
     * @return PlayerPoint
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set tournament
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Tournament $tournament
     * @return PlayerPoint
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

    /**
     * Set team
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Team $team
     * @return PlayerPoint
     */
    public function setTeam(\Lider\Bundle\LiderBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set player
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $player
     * @return PlayerPoint
     */
    public function setPlayer(\Lider\Bundle\LiderBundle\Entity\Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Player 
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set duel
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Duel $duel
     * @return PlayerPoint
     */
    public function setDuel(\Lider\Bundle\LiderBundle\Entity\Duel $duel = null)
    {
        $this->duel = $duel;

        return $this;
    }

    /**
     * Get duel
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Duel 
     */
    public function getDuel()
    {
        return $this->duel;
    }

    /**
     * Set question
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Question $question
     * @return PlayerPoint
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
