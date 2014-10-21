<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DuelQuestion class
 * @ORM\Table(name="duelquestion")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\MainRepository")
 */
class DuelQuestion extends Entity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Question",cascade={"persist"})
	 * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $question;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Duel",cascade={"persist"})
	 * @ORM\JoinColumn(name="duel_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $duel;
	
    /**
     * @ORM\ManyToOne(targetEntity="Game",cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */    
    private $game;		

    

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
     * Set starttime
     *
     * @param \DateTime $starttime
     * @return DuelQuestion
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;

        return $this;
    }

    /**
     * Get starttime
     *
     * @return \DateTime 
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set endtime
     *
     * @param \DateTime $endtime
     * @return DuelQuestion
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;

        return $this;
    }

    /**
     * Get endtime
     *
     * @return \DateTime 
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Set usehelp
     *
     * @param boolean $usehelp
     * @return DuelQuestion
     */
    public function setUsehelp($usehelp)
    {
        $this->usehelp = $usehelp;

        return $this;
    }

    /**
     * Get usehelp
     *
     * @return boolean 
     */
    public function getUsehelp()
    {
        return $this->usehelp;
    }

    /**
     * Set question
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Question $question
     * @return DuelQuestion
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

    /**
     * Set duel
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Duel $duel
     * @return DuelQuestion
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
     * Set game
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Game $game
     * @return DuelQuestion
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
}
