<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity Class
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Entity{
	
	 /**
     * @ORM\Column(type="boolean")
     */
    private $deleted = false;
    
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $entrydate;

     /**
      * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastupdate;


    /**
     * @ORM\PrePersist()
     */
    public function prePersist(){
        $this->entrydate = new \DateTime();
        $this->deleted = false;
        $this->lastupdate = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate(){
        $this->lastupdate = new \DateTime();
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Entity
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set entrydate
     *
     * @param \DateTime $entrydate
     * @return Entity
     */
    public function setEntrydate($entrydate)
    {
        $this->entrydate = $entrydate;

        return $this;
    }

    /**
     * Get entrydate
     *
     * @return \DateTime 
     */
    public function getEntrydate()
    {
        return $this->entrydate;
    }

    /**
     * Set lastupdate
     *
     * @param \DateTime $lastupdate
     * @return Entity
     */
    public function setLastupdate($lastupdate)
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    /**
     * Get lastupdate
     *
     * @return \DateTime 
     */
    public function getLastupdate()
    {
        return $this->lastupdate;
    }
}
