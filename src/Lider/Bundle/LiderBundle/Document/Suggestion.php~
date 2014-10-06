<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Suggestion
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
     * @MongoDB\String
     */
    private $subject;
    
    /**
     * @MongoDB\String
     */
    private $text;

    /**
     * @MongoDB\Date
     */
	private  $suggestionDate;



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
     * Set subject
     *
     * @param string $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set suggestionDate
     *
     * @param date $suggestionDate
     * @return self
     */
    public function setSuggestionDate($suggestionDate)
    {
        $this->suggestionDate = $suggestionDate;
        return $this;
    }

    /**
     * Get suggestionDate
     *
     * @return date $suggestionDate
     */
    public function getSuggestionDate()
    {
        return $this->suggestionDate;
    }
}
