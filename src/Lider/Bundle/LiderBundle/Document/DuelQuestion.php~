<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class DuelQuestion
{
    /**
     * @MongoDB\Id  
     */
    private $id;
    
    /**
     * @MongoDB\int
     */
    private $gameId;
        
    /**
     * @MongoDB\EmbedOne(targetDocument="Question") 
     */
    private $question;

	/**
     * @MongoDB\int
     */
    private $duelId;
   
    /**	
     * @MongoDB\Timestamp 
     */
    private $starttime; 

    /** 
     * @MongoDB\Timestamp 
     */
	private $endtime;
	
	/**
     * @MongoDB\Boolean 
     */
	private $usehelp = false;
	
    /**
     * @MongoDB\EmbedOne(targetDocument="Player") 
     */     
	private $player_one;
	
    /**
     * @MongoDB\EmbedOne(targetDocument="Player") 
     */     
	private $player_two;
	
    /**
     * @MongoDB\EmbedOne(targetDocument="Answer") 
     */
	private $answer_one;
	
    /**
     * @MongoDB\EmbedOne(targetDocument="Answer") 
     */
	private $answer_two;
	
	/**
     * @MongoDB\int
     */
	private $point_one = 0;
	
	/**
     * @MongoDB\int
     */
	private $point_two = 0;
		

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set gameId
     *
     * @param int $gameId
     * @return self
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
        return $this;
    }

    /**
     * Get gameId
     *
     * @return int $gameId
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set question
     *
     * @param Lider\Bundle\LiderBundle\Document\Question $question
     * @return self
     */
    public function setQuestion(\Lider\Bundle\LiderBundle\Document\Question $question)
    {
        $this->question = $question;
        return $this;
    }

    /**
     * Get question
     *
     * @return Lider\Bundle\LiderBundle\Document\Question $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set duelId
     *
     * @param int $duelId
     * @return self
     */
    public function setDuelId($duelId)
    {
        $this->duelId = $duelId;
        return $this;
    }

    /**
     * Get duelId
     *
     * @return int $duelId
     */
    public function getDuelId()
    {
        return $this->duelId;
    }

    /**
     * Set starttime
     *
     * @param timestamp $starttime
     * @return self
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
        return $this;
    }

    /**
     * Get starttime
     *
     * @return timestamp $starttime
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set endtime
     *
     * @param timestamp $endtime
     * @return self
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
        return $this;
    }

    /**
     * Get endtime
     *
     * @return timestamp $endtime
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Set usehelp
     *
     * @param boolean $usehelp
     * @return self
     */
    public function setUsehelp($usehelp)
    {
        $this->usehelp = $usehelp;
        return $this;
    }

    /**
     * Get usehelp
     *
     * @return boolean $usehelp
     */
    public function getUsehelp()
    {
        return $this->usehelp;
    }

    /**
     * Set playerOne
     *
     * @param Lider\Bundle\LiderBundle\Document\Player $playerOne
     * @return self
     */
    public function setPlayerOne(\Lider\Bundle\LiderBundle\Document\Player $playerOne)
    {
        $this->player_one = $playerOne;
        return $this;
    }

    /**
     * Get playerOne
     *
     * @return Lider\Bundle\LiderBundle\Document\Player $playerOne
     */
    public function getPlayerOne()
    {
        return $this->player_one;
    }

    /**
     * Set playerTwo
     *
     * @param Lider\Bundle\LiderBundle\Document\Player $playerTwo
     * @return self
     */
    public function setPlayerTwo(\Lider\Bundle\LiderBundle\Document\Player $playerTwo)
    {
        $this->player_two = $playerTwo;
        return $this;
    }

    /**
     * Get playerTwo
     *
     * @return Lider\Bundle\LiderBundle\Document\Player $playerTwo
     */
    public function getPlayerTwo()
    {
        return $this->player_two;
    }

    /**
     * Set answerOne
     *
     * @param Lider\Bundle\LiderBundle\Document\Answer $answerOne
     * @return self
     */
    public function setAnswerOne(\Lider\Bundle\LiderBundle\Document\Answer $answerOne)
    {
        $this->answer_one = $answerOne;
        return $this;
    }

    /**
     * Get answerOne
     *
     * @return Lider\Bundle\LiderBundle\Document\Answer $answerOne
     */
    public function getAnswerOne()
    {
        return $this->answer_one;
    }

    /**
     * Set answerTwo
     *
     * @param Lider\Bundle\LiderBundle\Document\Answer $answerTwo
     * @return self
     */
    public function setAnswerTwo(\Lider\Bundle\LiderBundle\Document\Answer $answerTwo)
    {
        $this->answer_two = $answerTwo;
        return $this;
    }

    /**
     * Get answerTwo
     *
     * @return Lider\Bundle\LiderBundle\Document\Answer $answerTwo
     */
    public function getAnswerTwo()
    {
        return $this->answer_two;
    }

    /**
     * Set pointOne
     *
     * @param int $pointOne
     * @return self
     */
    public function setPointOne($pointOne)
    {
        $this->point_one = $pointOne;
        return $this;
    }

    /**
     * Get pointOne
     *
     * @return int $pointOne
     */
    public function getPointOne()
    {
        return $this->point_one;
    }

    /**
     * Set pointTwo
     *
     * @param int $pointTwo
     * @return self
     */
    public function setPointTwo($pointTwo)
    {
        $this->point_two = $pointTwo;
        return $this;
    }

    /**
     * Get pointTwo
     *
     * @return int $pointTwo
     */
    public function getPointTwo()
    {
        return $this->point_two;
    }
}
