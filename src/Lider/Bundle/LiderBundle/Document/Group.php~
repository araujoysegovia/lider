<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Lider\Bundle\LiderBundle\Entity\Group as GroupEntity;

/**
 * @MongoDB\EmbeddedDocument 
 */
class Group extends GroupEntity
{
	/**
	* @MongoDB\Id  
	*/
	private $id;

	/**
	* @MongoDB\Int
	*/
	private $groupId;

    /**
     * @MongoDB\String
     */
    private $name;

    /**
     * @MongoDB\Boolean
     */
	private $active = true;

	

    public function getDataFromTournamentEntity(GroupEntity $group)
    {
        $this->setGroupId($group->getId());
        $this->setName($group->getName());
        $this->setActive($group->getActive());           

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

    /**
     * Set groupId
     *
     * @param int $groupId
     * @return self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Get groupId
     *
     * @return int $groupId
     */
    public function getGroupId()
    {
        return $this->groupId;
    }
}
