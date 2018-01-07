<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProfile
 *
 * @ORM\Table(name="sf_guard_user_profile")
 * @ORM\Entity
 */
class UserProfile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=255, nullable=true)
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="culture", type="string", length=10, nullable=true)
     */
    private $culture = 'es';

    /**
     * @ORM\OneToOne(targetEntity="Week", mappedBy="profile")
     */
    protected $week;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $centers
     * 
     * @ORM\ManyToMany(targetEntity="Center", mappedBy="profiles")
     */
    private $centers;

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
     * Set user
     *
     * @param \Blog\CoreBundle\Entity\User $user
     * @return UserProfile
     */
    public function setUser(\Blog\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Blog\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set plainPassword
     *
     * @param string $plainPassword
     * @return UserProfile
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get plainPassword
     *
     * @return string 
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UserProfile
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
     * Set email
     *
     * @param string $email
     * @return UserProfile
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
     * Set culture
     *
     * @param string $culture
     * @return UserProfile
     */
    public function setCulture($culture)
    {
        $this->culture = $culture;

        return $this;
    }

    /**
     * Get culture
     *
     * @return string 
     */
    public function getCulture()
    {
        return $this->culture;
    }

    /**
     * Set week
     *
     * @param \Blog\CoreBundle\Entity\Week $week
     * @return UserProfile
     */
    public function setWeek(\Blog\CoreBundle\Entity\Week $week = null)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return \Blog\CoreBundle\Entity\Week 
     */
    public function getWeek()
    {
        return $this->week;
    }

    public function __toString()
    {
        return $this->email;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->centers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add centers
     *
     * @param \Blog\CoreBundle\Entity\Center $centers
     * @return UserProfile
     */
    public function addCenter(\Blog\CoreBundle\Entity\Center $centers)
    {
        $this->centers[] = $centers;

        return $this;
    }

    /**
     * Remove centers
     *
     * @param \Blog\CoreBundle\Entity\Center $centers
     */
    public function removeCenter(\Blog\CoreBundle\Entity\Center $centers)
    {
        $this->centers->removeElement($centers);
    }

    /**
     * Get centers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCenters()
    {
        return $this->centers;
    }
}
