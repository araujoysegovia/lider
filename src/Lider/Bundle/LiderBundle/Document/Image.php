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
    
    /** @MongoDB\Field */
    private $name;

   	/**	@MongoDB\Field */
    private $mimetype;
   
    public function __construct()
    {
        $this->entity = new \Doctrine\Common\Collections\ArrayCollection();
        $this->metadata = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set uploadDate
     *
     * @param string $uploadDate
     * @return self
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
        return $this;
    }

    /**
     * Get uploadDate
     *
     * @return string $uploadDate
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set length
     *
     * @param string $length
     * @return self
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Get length
     *
     * @return string $length
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set chunkSize
     *
     * @param string $chunkSize
     * @return self
     */
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = $chunkSize;
        return $this;
    }

    /**
     * Get chunkSize
     *
     * @return string $chunkSize
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * Set md5
     *
     * @param string $md5
     * @return self
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
        return $this;
    }

    /**
     * Get md5
     *
     * @return string $md5
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * Set companyId
     *
     * @param string $companyId
     * @return self
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * Get companyId
     *
     * @return string $companyId
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Add entity
     *
     * @param AYS\ArchiveBundle\Document\ArchiveFileEntity $entity
     */
    public function addEntity(\AYS\ArchiveBundle\Document\ArchiveFileEntity $entity)
    {
        $this->entity[] = $entity;
    }

    /**
     * Remove entity
     *
     * @param AYS\ArchiveBundle\Document\ArchiveFileEntity $entity
     */
    public function removeEntity(\AYS\ArchiveBundle\Document\ArchiveFileEntity $entity)
    {
        $this->entity->removeElement($entity);
    }

    /**
     * Get entity
     *
     * @return Doctrine\Common\Collections\Collection $entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Add metadata
     *
     * @param AYS\ArchiveBundle\Document\ArchiveFileMetadata $metadata
     */
    public function addMetadata(\AYS\ArchiveBundle\Document\ArchiveFileMetadata $metadata)
    {
        $this->metadata[] = $metadata;
    }

    /**
     * Remove metadata
     *
     * @param AYS\ArchiveBundle\Document\ArchiveFileMetadata $metadata
     */
    public function removeMetadata(\AYS\ArchiveBundle\Document\ArchiveFileMetadata $metadata)
    {
        $this->metadata->removeElement($metadata);
    }

    /**
     * Get metadata
     *
     * @return Doctrine\Common\Collections\Collection $metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set fileId
     *
     * @param string $fileId
     * @return self
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
        return $this;
    }

    /**
     * Get fileId
     *
     * @return string $fileId
     */
    public function getFileId()
    {
        return $this->fileId;
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
