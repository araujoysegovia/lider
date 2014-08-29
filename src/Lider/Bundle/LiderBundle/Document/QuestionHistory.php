<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class QuestionHistory
{
    /**
     * @MongoDB\Id  
     */
    private $id;
    
    /**
     * @MongoDB\Int
     */
    private $playerId;
        
    /**
     * @MongoDB\Int
     */
    private $questionId;
    
    /**
     * @MongoDB\String
     */
    private $selectedAnswer;

    /**
     * @MongoDB\Boolean
     */
    private $duel;
    
    /**
     * @MongoDB\Int     
     */
    private $duelId;

    /**	
     * @MongoDB\Boolean
     */
    private $finished;
    
    /**
     * @MongoDB\Date
     */
    private $entryDate;
    
    /**
     * @MongoDB\String
     */
    private $answerOk;
    

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
     * Set playerId
     *
     * @param int $playerId
     * @return self
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
        return $this;
    }

    /**
     * Get playerId
     *
     * @return int $playerId
     */
    public function getPlayerId()
    {
        return $this->playerId;
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
     * Set selectedAnswer
     *
     * @param string $selectedAnswer
     * @return self
     */
    public function setSelectedAnswer($selectedAnswer)
    {
        $this->selectedAnswer = $selectedAnswer;
        return $this;
    }

    /**
     * Get selectedAnswer
     *
     * @return string $selectedAnswer
     */
    public function getSelectedAnswer()
    {
        return $this->selectedAnswer;
    }

    /**
     * Set duel
     *
     * @param boolean $duel
     * @return self
     */
    public function setDuel($duel)
    {
        $this->duel = $duel;
        return $this;
    }

    /**
     * Get duel
     *
     * @return boolean $duel
     */
    public function getDuel()
    {
        return $this->duel;
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
     * Set finished
     *
     * @param boolean $finished
     * @return self
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
        return $this;
    }

    /**
     * Get finished
     *
     * @return boolean $finished
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set entryDate
     *
     * @param date $entryDate
     * @return self
     */
    public function setEntryDate($entryDate)
    {
        $this->entryDate = $entryDate;
        return $this;
    }

    /**
     * Get entryDate
     *
     * @return date $entryDate
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * Set answerOk
     *
     * @param string $answerOk
     * @return self
     */
    public function setAnswerOk($answerOk)
    {
        $this->answerOk = $answerOk;
        return $this;
    }

    /**
     * Get answerOk
     *
     * @return string $answerOk
     */
    public function getAnswerOk()
    {
        return $this->answerOk;
    }
}
