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
     * @MongoDB\int
     */
    private $questionId;

	/**
     * @MongoDB\int
     */
    private $duelId;
   
    /**	@MongoDB\Timestamp */
    private $starttime; 

    /**	@MongoDB\Timestamp */
	private $endtime;
	
	/**	@MongoDB\Boolean */
	private $usehelp = false;
	
	/**
     * @MongoDB\int
     */
	private $player_one_id;
	
	/**
     * @MongoDB\int
     */
	private $player_two_id;
	
	/**
     * @MongoDB\int
     */
	private $answer_one_id;
	
	/**
     * @MongoDB\int
     */
	private $answer_two_id;
	
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
     * Set questionId
     *
     * @param int $questionId
     * @return self
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
        return $this;
    }

    /**
     * Get questionId
     *
     * @return int $questionId
     */
    public function getQuestionId()
    {
        return $this->questionId;
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
    	return $this->questionId;
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
     * @return int $starttime
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
     * Set player_one_id
     *
     * @param int $player_one_id
     * @return self
     */
    public function setPlayer_one_id($player_one_id)
    {
    	$this->player_one_id = $player_one_id;
    	return $this;
    }
    
    /**
     * Get player_one_id
     *
     * @return int $player_one_id
     */
    public function getPlayer_one_id()
    {
    	return $this->player_one_id;
    }    
    
    /**
     * Set player_two_id
     *
     * @param int $player_two_id
     * @return self
     */
    public function setPlayer_two_id($player_two_id)
    {
    	$this->player_two_id = $player_two_id;
    	return $this;
    }
    
    /**
     * Get player_two_id
     *
     * @return int $player_two_id
     */
    public function getPlayer_two_id()
    {
    	return $this->player_two_id;
    } 

    /**
     * Set answer_one_id
     *
     * @param int $answer_one_id
     * @return self
     */
    public function setAnswer_one_id($answer_one_id)
    {
    	$this->answer_one_id = $answer_one_id;
    	return $this;
    }
    
    /**
     * Get answer_one_id
     *
     * @return int $answer_one_id
     */
    public function getAnswer_one_id()
    {
    	return $this->answer_one_id;
    }   

    /**
     * Set answer_two_id
     *
     * @param int $answer_two_id
     * @return self
     */
    public function setAnswer_two_id($answer_two_id)
    {
    	$this->answer_two_id = $answer_two_id;
    	return $this;
    }
    
    /**
     * Get answer_two_id
     *
     * @return int $answer_two_id
     */
    public function getAnswer_two_id()
    {
    	return $this->answer_two_id;
    }    
    
    /**
     * Set point_one
     *
     * @param int $point_one
     * @return self
     */
    public function setPoint_one($point_one)
    {
    	$this->point_one = $point_one;
    	return $this;
    }
    
    /**
     * Get point_one
     *
     * @return int $point_one
     */
    public function getPoint_one()
    {
    	return $this->point_one;
    }   

    /**
     * Set point_two
     *
     * @param int $point_two
     * @return self
     */
    public function setPoint_two($point_two)
    {
    	$this->point_two = $point_two;
    	return $this;
    }
    
    /**
     * Get point_two
     *
     * @return int $point_two
     */
    public function getPoint_two()
    {
    	return $this->point_two;
    }    

    /**
     * Set playerOneId
     *
     * @param int $playerOneId
     * @return self
     */
    public function setPlayerOneId($playerOneId)
    {
        $this->player_one_id = $playerOneId;
        return $this;
    }

    /**
     * Get playerOneId
     *
     * @return int $playerOneId
     */
    public function getPlayerOneId()
    {
        return $this->player_one_id;
    }

    /**
     * Set playerTwoId
     *
     * @param int $playerTwoId
     * @return self
     */
    public function setPlayerTwoId($playerTwoId)
    {
        $this->player_two_id = $playerTwoId;
        return $this;
    }

    /**
     * Get playerTwoId
     *
     * @return int $playerTwoId
     */
    public function getPlayerTwoId()
    {
        return $this->player_two_id;
    }

    /**
     * Set answerOneId
     *
     * @param int $answerOneId
     * @return self
     */
    public function setAnswerOneId($answerOneId)
    {
        $this->answer_one_id = $answerOneId;
        return $this;
    }

    /**
     * Get answerOneId
     *
     * @return int $answerOneId
     */
    public function getAnswerOneId()
    {
        return $this->answer_one_id;
    }

    /**
     * Set answerTwoId
     *
     * @param int $answerTwoId
     * @return self
     */
    public function setAnswerTwoId($answerTwoId)
    {
        $this->answer_two_id = $answerTwoId;
        return $this;
    }

    /**
     * Get answerTwoId
     *
     * @return int $answerTwoId
     */
    public function getAnswerTwoId()
    {
        return $this->answer_two_id;
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
}
