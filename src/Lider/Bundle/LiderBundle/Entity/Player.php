<?php
namespace Lider\Bundle\LiderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser as BaseOAuthUser;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

/**
 * Player class
 * @ORM\Table(name="player", uniqueConstraints={@ORM\UniqueConstraint(name="player_email", columns={"email"})}))
 * @ORM\Entity(repositoryClass="Lider\Bundle\LiderBundle\Repository\PlayerRepository")
 */
class Player extends Entity implements AdvancedUserInterface, \Serializable{
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=100)
	 * @Assert\Length(max=100)
	 * @Assert\Email()
	 */
	private $email;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=100)
	 * @Assert\Length(max=100)
	 */
	private $name;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=100)
	 * @Assert\Length(max=100)
	 */
	private $lastname;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=100)
	 * @Assert\Length(max=100)
	 */
	private $image;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Office",cascade={"persist"})
	 * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
	 * @Assert\NotBlank()
	 */
	private $office;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Role",cascade={"persist"})
	 * @ORM\JoinTable(name="users_role",
	 *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 * )
	 * @Assert\NotBlank()
	 */
	private $roles;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Team",cascade={"persist"})
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
	 */
	private $team;
	
	/**
	 * @ORM\Column(type="string", length=40)
     * @Assert\Length(max=40)
	 * @Assert\NotBlank()
	 */
	private $password;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)	 	 
	 */
	private $wonGames;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $wonLost;
	
	/**
	 * @ORM\Column(type="boolean", nullable = true)	 	 
	 */
	private $active = true;
	 
	public function serialize()
	{
		return serialize(array($this->getId(), $this->getUsername()));
	}
	
	public function unserialize($data)
	{
		list($this->id, $this->username)= unserialize($data);
	}
	
	public function getSalt()
	{
		return "dialboxes";
	}
	
	public function eraseCredentials()
	{
		return false;
	}
	
	public function equals($user)
	{		
		return $user->getUsername() == $this->getUsername();
	}
	
	public function isAccountNonExpired()
	{
		return true;
	}
	
	public function isAccountNonLocked()
	{
		return true;
	}
	
	public function isCredentialsNonExpired()
	{
		return true;
	}
	
	public function isEnabled()
	{
		return true;
	}
	
	public function getRoles()
	{
		/*if(is_array($this->roles))
		{
			return $this->roles;
		}
		elseif(is_object($this->roles))
		{
			return $this->roles->toArray();
		}*/
		return $this->roles->toArray();
	}
	
	public function getPassword()
	{
		return $this->password;
	}
	
	public function getUsername()
	{
		return $this->email;
	}
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->password = "araujo123";
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Player
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Player
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
     * Set lastname
     *
     * @param string $lastname
     * @return Player
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Player
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set office
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Office $office
     * @return Player
     */
    public function setOffice(\Lider\Bundle\LiderBundle\Entity\Office $office = null)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Office 
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Add roles
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Role $roles
     * @return Player
     */
    public function addRole(\Lider\Bundle\LiderBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Role $roles
     */
    public function removeRole(\Lider\Bundle\LiderBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set team
     *
     * @param \Lider\Bundle\LiderBundle\Entity\Team $team
     * @return Player
     */
    public function setTeam(\Lider\Bundle\LiderBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \Lider\Bundle\LiderBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Player
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set wonGames
     *
     * @param integer $wonGames
     * @return Player
     */
    public function setWonGames($wonGames)
    {
        $this->wonGames = $wonGames;

        return $this;
    }

    /**
     * Get wonGames
     *
     * @return integer 
     */
    public function getWonGames()
    {
        return $this->wonGames;
    }

    /**
     * Set wonLost
     *
     * @param integer $wonLost
     * @return Player
     */
    public function setWonLost($wonLost)
    {
        $this->wonLost = $wonLost;

        return $this;
    }

    /**
     * Get wonLost
     *
     * @return integer 
     */
    public function getWonLost()
    {
        return $this->wonLost;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Player
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }
}
