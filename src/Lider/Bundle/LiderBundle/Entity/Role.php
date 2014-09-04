<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Role class
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\MainRepository")
 */
class Role extends Entity implements RoleInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank()
     * @Assert\Length(max=100)
	 */
	private $name;
	
	/**
	 * @ORM\Column(type="text", nullable=true, length=1000)
     * @Assert\Length(max=1000)
	 */
	private $description;
    

    public function serialize()
    {
        return serialize(array($this->getId()));
    }   
    
    public function unserialize($data)
    {
        list($this->id)= unserialize($data);
    }

	public function getRole(){
		return 'ROLE_'.strtoupper($this->getName());
	}

	public function getId()
	{
		return $this->id;
	}


    /**
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
