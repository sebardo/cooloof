<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Week
 *
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="Blog\CoreBundle\Repository\WeekRepository")
 */
class Week
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
     * @var \Blog\CoreBundle\Entity\Center $center
     *
     * @ORM\ManyToOne(targetEntity="\Blog\CoreBundle\Entity\Center", inversedBy="weeks")
     * @ORM\JoinColumn(name="summer_fun_center_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $center;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="starts_at", type="date")
     */
    private $startsAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ends_at", type="date")
     */
    private $endsAt;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $posts
     *
     * @ORM\OneToMany(targetEntity="\Blog\CoreBundle\Entity\Post", mappedBy="week")
     */
    private $posts;

    /**
     * @ORM\OneToOne(targetEntity="UserProfile", inversedBy="week", cascade={"persist"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=true)
     */
    private $profile;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $translations
     *
     * @ORM\OneToMany(targetEntity="\Blog\CoreBundle\Entity\WeekTranslation", mappedBy="id")
     */
    private $translations;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->translations = new ArrayCollection();
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
     * Set center
     *
     * @param string $center
     * @return Week
     */
    public function setCenter($center)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get center
     *
     * @return string 
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Set startsAt
     *
     * @param \DateTime $startsAt
     * @return Week
     */
    public function setStartsAt($startsAt)
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * Get startsAt
     *
     * @return \DateTime 
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * Set endsAt
     *
     * @param \DateTime $endsAt
     * @return Week
     */
    public function setEndsAt($endsAt)
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * Get endsAt
     *
     * @return \DateTime 
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * Add posts
     *
     * @param \Blog\CoreBundle\Entity\Post $posts
     * @return Week
     */
    public function addPost(\Blog\CoreBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Blog\CoreBundle\Entity\Post $posts
     */
    public function removePost(\Blog\CoreBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function __toString()
    {
        $center = '';
        if ($this->center)
        {
            foreach ($this->center->getTranslations() as $translation)
            {
                if ($translation->getTitle()) {
                    $center = $translation->getTitle();
                    break;
                }
            }
        }

        $center = ($center ? $center . ' - ' : '') . 'Del ' . $this->startsAt->format('d/m/Y') . ' al ' . $this->endsAt->format('d/m/Y');

        foreach ($this->getTranslations() as $translation)
        {
            if ($translation->getSchedule()) {
                $center .= ' - ' . $translation->getSchedule();
                break;
            }
        }

        return $center;
    }

    public function getCenterName()
    {
        $center = '';
        if ($this->center)
        {
            foreach ($this->center->getTranslations() as $translation)
            {
                if ($translation->getTitle()) {
                    $center = $translation->getTitle();
                    break;
                }
            }
        }

        return $center;
    }

    public function getSchedule()
    {
        $schedule = '';
        foreach ($this->getTranslations() as $translation)
        {
            if ($translation->getSchedule()) {
                $schedule = $translation->getSchedule();
                break;
            }
        }

        return $schedule;
    }

    /**
     * Set profile
     *
     * @param \Blog\CoreBundle\Entity\UserProfile $profile
     * @return Week
     */
    public function setProfile(\Blog\CoreBundle\Entity\UserProfile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \Blog\CoreBundle\Entity\UserProfile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Add translations
     *
     * @param \Blog\CoreBundle\Entity\WeekTranslation $translations
     * @return Week
     */
    public function addTranslation(\Blog\CoreBundle\Entity\WeekTranslation $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Blog\CoreBundle\Entity\WeekTranslation $translations
     */
    public function removeTranslation(\Blog\CoreBundle\Entity\WeekTranslation $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}
