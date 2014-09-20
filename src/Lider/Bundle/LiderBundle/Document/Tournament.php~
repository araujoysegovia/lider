<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Lider\Bundle\LiderBundle\Entity\Tournament as TournamentEntity;

/**
 * @MongoDB\EmbeddedDocument 
 */
class Tournament extends TournamentEntity
{
	/**
	* @MongoDB\Id  
	*/
	private $id;

	/**
	* @MongoDB\Int
	*/
	private  $tournamentId;

    /**
     * @MongoDB\String
     */
    private $name;	

    /**
     * @MongoDB\Date
     */
	private $startdate;
	
    /**
     * @MongoDB\Date
     */
	private $enddate;
	
    /**
     * @MongoDB\Boolean
     */
	private $active = true;

	

    public function getDataFromTournamentEntity(TournamentEntity $tournament)
    {
        $this->setTournamentId($tournament->getId());
        $this->setName($tournament->getName());
        $this->setStartdate($tournament->getStartdate());
        $this->setEnddate($tournament->getEnddate());
        $this->setActive($tournament->getActive());           
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
     * Set tournamentId
     *
     * @param int $tournamentId
     * @return self
     */
    public function setTournamentId($tournamentId)
    {
        $this->tournamentId = $tournamentId;
        return $this;
    }

    /**
     * Get tournamentId
     *
     * @return int $tournamentId
     */
    public function getTournamentId()
    {
        return $this->tournamentId;
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
     * Set startdate
     *
     * @param date $startdate
     * @return self
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;
        return $this;
    }

    /**
     * Get startdate
     *
     * @return date $startdate
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param date $enddate
     * @return self
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;
        return $this;
    }

    /**
     * Get enddate
     *
     * @return date $enddate
     */
    public function getEnddate()
    {
        return $this->enddate;
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
