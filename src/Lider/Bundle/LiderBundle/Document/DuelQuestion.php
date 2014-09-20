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
	private $player;
	
    /**
     * @MongoDB\EmbedOne(targetDocument="Answer") 
     */
	private $answer;

	/**
     * @MongoDB\int
     */
	private $point = 0;
		


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
     * Set player
     *
     * @param Lider\Bundle\LiderBundle\Document\Player $player
     * @return self
     */
    public function setPlayer(\Lider\Bundle\LiderBundle\Document\Player $player)
    {
        $this->player = $player;
        return $this;
    }

    /**
     * Get player
     *
     * @return Lider\Bundle\LiderBundle\Document\Player $player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set answer
     *
     * @param Lider\Bundle\LiderBundle\Document\Answer $answer
     * @return self
     */
    public function setAnswer(\Lider\Bundle\LiderBundle\Document\Answer $answer)
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * Get answer
     *
     * @return Lider\Bundle\LiderBundle\Document\Answer $answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set point
     *
     * @param int $point
     * @return self
     */
    public function setPoint($point)
    {
        $this->point = $point;
        return $this;
    }

    /**
     * Get point
     *
     * @return int $point
     */
    public function getPoint()
    {
        return $this->point;
    }
}
