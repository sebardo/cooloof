<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CenterTranslation
 *
 * @ORM\Table(name="course_i18n")
 * @ORM\Entity
 */
class WeekTranslation
{
    /**
     * @var \Blog\CoreBundle\Entity\Week $id
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Blog\CoreBundle\Entity\Week", inversedBy="translations")
     * @ORM\JoinColumn(name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule", type="string", length=255)
     */
    private $schedule;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="culture", type="string", length=7)
     */
    private $culture;

    /**
     * Set culture
     *
     * @param string $culture
     * @return CenterTranslation
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
     * Set id
     *
     * @param \Blog\CoreBundle\Entity\Center $id
     * @return CenterTranslation
     */
    public function setId(\Blog\CoreBundle\Entity\Center $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return \Blog\CoreBundle\Entity\Center 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set schedule
     *
     * @param string $schedule
     * @return WeekTranslation
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return string 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
}
