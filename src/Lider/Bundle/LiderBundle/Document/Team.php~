<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Lider\Bundle\LiderBundle\Entity\Team as TeamEntity;

/**
 * @MongoDB\EmbeddedDocument 
 */
class Team extends TeamEntity
{
	/**
	* @MongoDB\Id  
	*/
	private $id;

	/**
	* @MongoDB\Int
	*/
	private  $teamId;

    /**
     * @MongoDB\String
     */
    private $name;
	
    /**
     * @MongoDB\Boolean
     */
	private $active = true;

	

    public function getDataFromTournamentEntity(TeamEntity $team)
    {
        $this->setTournamentId($team->getId());
        $this->setName($team->getName());
        $this->setActive($team->getActive());           
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
     * Set teamId
     *
     * @param int $teamId
     * @return self
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
        return $this;
    }

    /**
     * Get teamId
     *
     * @return int $teamId
     */
    public function getTeamId()
    {
        return $this->teamId;
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
     * Set active
     *
     * @param boolean $active
     * @return self
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean $active
     */
    public function getActive()
    {
        return $this->active;
    }
}
