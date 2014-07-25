<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Player Tournament Point class
 * @ORM\Table(name="playertournamentpoint")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\MainRepository")
 */
class PlayerTournamentPoint extends Entity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Player",cascade={"persist"})
	 * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $player;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Team",cascade={"persist"})
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $team;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Tournament",cascade={"persist"})
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $tournament;
	
	/**
	 * @ORM\Column(type="integer")
	 * @Assert\NotBlank()
	 */
	private $point = 0;

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
     * Set point
     *
     * @param integer $point
     * @return PlayerTournamentPoint
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer 
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set player
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $player
     * @return PlayerTournamentPoint
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
     * Set team
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Team $team
     * @return PlayerTournamentPoint
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
     * Set tournament
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Tournament $tournament
     * @return PlayerTournamentPoint
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
