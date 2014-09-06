<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document(repositoryClass="Lider\Bundle\LiderBundle\Repository\QuestionHistoryRepository")
 */
class QuestionHistory
{
    /**
     * @MongoDB\Id  
     */
    private $id;
    
    /**
     * @MongoDB\EmbedOne(targetDocument="Player") 
     */        
    private $player; 
        
    /**
     * @MongoDB\EmbedOne(targetDocument="Question") 
     */
    private $question;
    
    /**
     * @MongoDB\EmbedOne(targetDocument="Answer") 
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
     * @MongoDB\EmbedOne(targetDocument="Answer") 
     */
    private $answerOk;
        
    /** 
     * @MongoDB\Boolean
     */
    private $find = false;

    /** 
     * @MongoDB\Boolean
     */
    private $timeOut = false;

    /**
     * @MongoDB\EmbedMany(targetDocument="Answer") 
     */        
    private $answers; 

    /**
     * @MongoDB\EmbedOne(targetDocument="Tournament") 
     */ 
    private $tournament;    

    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set selectedAnswer
     *
     * @param Lider\Bundle\LiderBundle\Document\Answer $selectedAnswer
     * @return self
     */
    public function setSelectedAnswer(\Lider\Bundle\LiderBundle\Document\Answer $selectedAnswer)
    {
        $this->selectedAnswer = $selectedAnswer;
        return $this;
    }

    /**
     * Get selectedAnswer
     *
     * @return Lider\Bundle\LiderBundle\Document\Answer $selectedAnswer
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
     * @param Lider\Bundle\LiderBundle\Document\Answer $answerOk
     * @return self
     */
    public function setAnswerOk(\Lider\Bundle\LiderBundle\Document\Answer $answerOk)
    {
        $this->answerOk = $answerOk;
        return $this;
    }

    /**
     * Get answerOk
     *
     * @return Lider\Bundle\LiderBundle\Document\Answer $answerOk
     */
    public function getAnswerOk()
    {
        return $this->answerOk;
    }

    /**
     * Set find
     *
     * @param boolean $find
     * @return self
     */
    public function setFind($find)
    {
        $this->find = $find;
        return $this;
    }

    /**
     * Get find
     *
     * @return boolean $find
     */
    public function getFind()
    {
        return $this->find;
    }

    /**
     * Add answer
     *
     * @param Lider\Bundle\LiderBundle\Document\Answer $answer
     */
    public function addAnswer(\Lider\Bundle\LiderBundle\Document\Answer $answer)
    {
        $this->answers[] = $answer;
    }

    /**
     * Remove answer
     *
     * @param Lider\Bundle\LiderBundle\Document\Answer $answer
     */
    public function removeAnswer(\Lider\Bundle\LiderBundle\Document\Answer $answer)
    {
        $this->answers->removeElement($answer);
    }

    /**
     * Get answers
     *
     * @return Doctrine\Common\Collections\Collection $answers
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set timeOut
     *
     * @param boolean $timeOut
     * @return self
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    /**
     * Get timeOut
     *
     * @return boolean $timeOut
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * Set tournament
     *
     * @param Lider\Bundle\LiderBundle\Document\Tournament $tournament
     * @return self
     */
    public function setTournament(\Lider\Bundle\LiderBundle\Document\Tournament $tournament)
    {
        $this->tournament = $tournament;
        return $this;
    }

    /**
     * Get tournament
     *
     * @return Lider\Bundle\LiderBundle\Document\Tournament $tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }
}
