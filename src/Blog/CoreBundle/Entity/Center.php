<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Center
 *
 * @ORM\Table(name="summer_fun_center")
 * @ORM\Entity(repositoryClass="Blog\CoreBundle\Repository\CenterRepository")
 */
class Center
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
     * @ORM\ManyToMany(targetEntity="UserProfile")
     * @ORM\JoinTable(name="summer_fun_center_has_profile",
     *      joinColumns={@ORM\JoinColumn(name="summer_fun_center_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")}
     *      )
     **/
    private $profiles;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $weeks
     *
     * @ORM\OneToMany(targetEntity="\Blog\CoreBundle\Entity\Week", mappedBy="center")
     */
    private $weeks;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $uploads
     *
     * @ORM\OneToMany(targetEntity="\Blog\CoreBundle\Entity\Upload", mappedBy="center")
     */
    private $uploads;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $translations
     *
     * @ORM\OneToMany(targetEntity="\Blog\CoreBundle\Entity\CenterTranslation", mappedBy="id")
     */
    private $translations;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        $this->weeks = new ArrayCollection();
        $this->uploads = new ArrayCollection();
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
     * Add profiles
     *
     * @param \Blog\CoreBundle\Entity\UserProfile $profiles
     * @return Center
     */
    public function addProfile(\Blog\CoreBundle\Entity\UserProfile $profiles)
    {
        $this->profiles[] = $profiles;

        return $this;
    }

    /**
     * Remove profiles
     *
     * @param \Blog\CoreBundle\Entity\UserProfile $profiles
     */
    public function removeProfile(\Blog\CoreBundle\Entity\UserProfile $profiles)
    {
        $this->profiles->removeElement($profiles);
    }

    /**
     * Get profiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * Add translations
     *
     * @param \Blog\CoreBundle\Entity\CenterTranslation $translations
     * @return Center
     */
    public function addTranslation(\Blog\CoreBundle\Entity\CenterTranslation $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Blog\CoreBundle\Entity\CenterTranslation $translations
     */
    public function removeTranslation(\Blog\CoreBundle\Entity\CenterTranslation $translations)
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

    /**
     * Add weeks
     *
     * @param \Blog\CoreBundle\Entity\Week $weeks
     * @return Center
     */
    public function addWeek(\Blog\CoreBundle\Entity\Week $weeks)
    {
        $this->weeks[] = $weeks;

        return $this;
    }

    /**
     * Remove weeks
     *
     * @param \Blog\CoreBundle\Entity\Week $weeks
     */
    public function removeWeek(\Blog\CoreBundle\Entity\Week $weeks)
    {
        $this->weeks->removeElement($weeks);
    }

    /**
     * Get weeks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWeeks()
    {
        return $this->weeks;
    }

    /**
     * Add uploads
     *
     * @param \Blog\CoreBundle\Entity\Upload $uploads
     * @return Center
     */
    public function addUpload(\Blog\CoreBundle\Entity\Upload $uploads)
    {
        $this->uploads[] = $uploads;

        return $this;
    }

    /**
     * Remove uploads
     *
     * @param \Blog\CoreBundle\Entity\Upload $uploads
     */
    public function removeUpload(\Blog\CoreBundle\Entity\Upload $uploads)
    {
        $this->uploads->removeElement($uploads);
    }

    /**
     * Get uploads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUploads()
    {
        return $this->uploads;
    }

    public function __toString()
    {
        foreach ($this->getTranslations() as $translation)
        {
            if ($translation->getTitle()) {
                return $translation->getTitle();
            }
        }

        return '';
    }
}
