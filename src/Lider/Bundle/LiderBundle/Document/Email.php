<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Email
{
	/**
	 * @MongoDB\Id
	 */
	private $id;
	
	/**
	 * @MongoDB\Date
     * @Assert\DateTime()
	 */
	private $datetime;
	
	/**
	 * @MongoDB\String
	 */
	private $subject;
	
	/**
	 * @MongoDB\String
	 */
	private $body;
	
	/**
	 * @MongoDB\String
     * @Assert\Email()
	 */
	private $to;
	
	/**
	 * @MongoDB\String
     * @Assert\Email()
	 */
	private $from;
	
	/**
     * @MongoDB\ReferenceOne(targetDocument="EmailState", simple=true)
     */
	private $state;
	
	/**
	 * @MongoDB\String
	 */
	private $providerId;

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
     * Set datetime
     *
     * @param date $datetime
     * @return self
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * Get datetime
     *
     * @return date $datetime
     */
    public function getDatetime()
    {
        return $this->datetime;
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
     * Set body
     *
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return string $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set to
     *
     * @param string $to
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Get to
     *
     * @return string $to
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set from
     *
     * @param string $from
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Get from
     *
     * @return string $from
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set state
     *
     * @param Lider\Bundle\LiderBundle\Document\EmailState $state
     * @return self
     */
    public function setState(\Lider\Bundle\LiderBundle\Document\EmailState $state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return Lider\Bundle\LiderBundle\Document\EmailState $state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set providerId
     *
     * @param string $providerId
     * @return self
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;
        return $this;
    }

    /**
     * Get providerId
     *
     * @return string $providerId
     */
    public function getProviderId()
    {
        return $this->providerId;
    }
}
