<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Lider\Bundle\LiderBundle\Entity\Player as PlayerEntity;

/**
 * @MongoDB\EmbeddedDocument 
 */
class Player extends PlayerEntity
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
     * @MongoDB\String  
     */
    private $email;

    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\String
     */
    private $lastname;

    /**
     * @MongoDB\String  
     */
    private $image;


    /**
     * Get mongoId
     *
     * @return id $mongoId
     */
    public function getMongoId()
    {
        return $this->mongoId;
    }

    public function getDataFromPlayerEntity(PlayerEntity $player)
    {
        $this->setPlayerId($player->getId());
        $this->setEmail($player->getEmail());
        $this->setName($player->getName());
        $this->setLastname($player->getLastname());
        $this->setImage($player->getImage());       

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
     * Set email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string $lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }
}
