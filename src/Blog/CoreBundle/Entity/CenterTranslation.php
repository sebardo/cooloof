<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CenterTranslation
 *
 * @ORM\Table(name="summer_fun_center_i18n")
 * @ORM\Entity
 */
class CenterTranslation
{
    /**
     * @var \Blog\CoreBundle\Entity\Center $id
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Blog\CoreBundle\Entity\Center", inversedBy="translations")
     * @ORM\JoinColumn(name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="culture", type="string", length=7)
     */
    private $culture;

    /**
     * Set title
     *
     * @param string $title
     * @return CenterTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

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
}
