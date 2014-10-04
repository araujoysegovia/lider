<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Image
{
    /**
     * @MongoDB\Id  
     */
    private $id;
    
    /** @MongoDB\String */
    private $name;

    /** @MongoDB\File */
    private $file;

    /** @MongoDB\String */
	private $mimetype;
	
	/** @MongoDB\String */
	private $entity;
	
	/** @MongoDB\Int */
	private  $entityId;	
	
	/** @MongoDB\Boolean */
	private $deleted;


	public function __construct()
	{
		$this->deleted = false;
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
     * Set file
     *
     * @param file $file
     * @return self
     */
    public function setFile($file)
    {
    	$this->file = $file;
    	return $this;
    }
    
    /**
     * Get file
     *
     * @return file $file
     */
    public function getFile()
    {
    	return $this->file;
    }
    
    /**
     * Set mimetype
     *
     * @param string $mimetype
     * @return self
     */
    public function setMimetype($mimetype)
    {
    	$this->mimetype = $mimetype;
    	return $this;
    }
    
    /**
     * Get mimetype
     *
     * @return string $mimetype
     */
    public function getMimetype()
    {
    	return $this->mimetype;
    }


    /**
     * Set entity
     *
     * @param string $entity
     * @return self
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get entity
     *
     * @return string $entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set entityId
     *
     * @param int $entityId
     * @return self
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * Get entityId
     *
     * @return int $entityId
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean $deleted
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
