<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DuelQuestion class
 * @ORM\Table(name="duelquestion")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\MainRepository")
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
	 * @ORM\Column(type="time")
	 * @Assert\NotBlank()
	 */
	private $starttime;
	
	/**
	 * @ORM\Column(type="time")
	 * @Assert\NotBlank()
	 */
	private $endtime;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $usehelp = false;
	
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
	 * @ORM\ManyToOne(targetEntity="Answer",cascade={"persist"})
	 * @ORM\JoinColumn(name="answerone_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $answer_one;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Answer",cascade={"persist"})
	 * @ORM\JoinColumn(name="answertwo_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $answer_two;
	
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
     * Set point_one
     *
     * @param integer $pointOne
     * @return DuelQuestion
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
     * @return DuelQuestion
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
     * Set player_one
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Player $playerOne
     * @return DuelQuestion
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
     * @return DuelQuestion
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
     * Set answer_one
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Answer $answerOne
     * @return DuelQuestion
     */
    public function setAnswerOne(\Lider\Bundle\LiderBundle\Entity\Answer $answerOne = null)
    {
        $this->answer_one = $answerOne;

        return $this;
    }

    /**
     * Get answer_one
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Answer 
     */
    public function getAnswerOne()
    {
        return $this->answer_one;
    }

    /**
     * Set answer_two
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Answer $answerTwo
     * @return DuelQuestion
     */
    public function setAnswerTwo(\Lider\Bundle\LiderBundle\Entity\Answer $answerTwo = null)
    {
        $this->answer_two = $answerTwo;

        return $this;
    }

    /**
     * Get answer_two
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Answer 
     */
    public function getAnswerTwo()
    {
        return $this->answer_two;
    }
}
