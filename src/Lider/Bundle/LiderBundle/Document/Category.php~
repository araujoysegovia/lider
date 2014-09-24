<?php
namespace Lider\Bundle\LiderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Lider\Bundle\LiderBundle\Entity\Category as CategoryEntity;

/**
 * @MongoDB\EmbeddedDocument 
 */
class Category extends CategoryEntity
{
	/**
     * @MongoDB\Id  
     */
    private $id;
    
    /**
     * @MongoDB\Int
     */
    private  $categoryId;

    /**
     * @MongoDB\String
     */
    private $name;

    public function getDataFromCategoryEntity(CategoryEntity $category)
    {
        $this->setCategoryId($category->getId());
        $this->setName($category->getName());        
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
     * Set categoryId
     *
     * @param int $categoryId
     * @return self
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * Get categoryId
     *
     * @return int $categoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
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
}
