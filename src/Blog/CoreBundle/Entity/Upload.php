<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PEC\CoreBundle\Entity\Upload
 *
 * @ORM\Table(name="upload")
 * @ORM\Entity(repositoryClass="Blog\CoreBundle\Repository\UploadRepository")
 * @Gedmo\TranslationEntity(class="Blog\CoreBundle\Entity\UploadTranslation")
 */

class Upload
{
    const UPLOAD_DIR = './../../web/uploads/assets/files/';
    const UPLOAD_TMP_DIR = './../../web/uploads/tmp/assets/files/';
    const UPLOAD_WEB_DIR = 'uploads/assets/files/';
    const UPLOAD_TMP_WEB_DIR = 'uploads/tmp/assets/files/';

    const TYPE_BANNER_IMAGE = 1;
    const TYPE_ATTACHMENT = 2;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Gedmo\Translatable
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string $file
     *
     * @ORM\Column(name="file", type="string", length=255)
     * @Gedmo\Translatable
     * @Assert\NotBlank()
     */
    private $file;

    /**
     * @var string $link
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(name="type", type="smallint")
     * @Assert\NotBlank()
     */
    protected $type = self::TYPE_BANNER_IMAGE;

    /**
     * @var \Blog\CoreBundle\Entity\Center $center
     *
     * @ORM\ManyToOne(targetEntity="\Blog\CoreBundle\Entity\Center", inversedBy="uploads")
     * @ORM\JoinColumn(name="summer_fun_center_id", referencedColumnName="id", onDelete="RESTRICT")
     *
     * @Assert\NotBlank()
     */
    private $center;

    /**
     * @ORM\Column(name="`order`", type="smallint", nullable=true)
     */
    private $order;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var integer
     *
     * @Assert\Regex("/^\d+$/")
     */
    private $editId;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Blog\CoreBundle\Entity\UploadTranslation",
     *   mappedBy="object",
     *   cascade={"all"}
     * )
     * @Assert\Valid
     */
    private $translations;

    public function __construct()
    {
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
     * Set name
     *
     * @param string $name
     * @return Upload
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
     * Set file
     *
     * @param string $file
     * @return Upload
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Upload
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Upload
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Get editId
     *
     * @return integer
     */
    public function getEditId()
    {
        return $this->editId;
    }

    /**
     * Set editId
     *
     * @param integer $editId
     * @return Banner
     */
    public function setEditId($editId)
    {
        $this->editId = $editId;

        return $this;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Upload
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeString()
    {
        switch ($this->type)
        {
            case self::TYPE_BANNER_IMAGE:
                return 'upload.type_banner_image';
            case self::TYPE_ATTACHMENT:
                return 'upload.type_attachment';
        }

        return 'upload.type_undefined';
    }

    public function getChoices()
    {
        return array(self::TYPE_BANNER_IMAGE => 'upload.type_banner_image',
            self::TYPE_ATTACHMENT => 'upload.type_attachment');
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Upload
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Add translations
     *
     * @param \Blog\CoreBundle\Entity\UploadTranslation $translations
     * @return Upload
     */
    public function addTranslation(\Blog\CoreBundle\Entity\UploadTranslation $translations)
    {
        $translations->setObject($this);
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Blog\CoreBundle\Entity\UploadTranslation $translations
     */
    public function removeTranslation(\Blog\CoreBundle\Entity\UploadTranslation $translations)
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
     * Set center
     *
     * @param \Blog\CoreBundle\Entity\Center $center
     * @return Upload
     */
    public function setCenter(\Blog\CoreBundle\Entity\Center $center = null)
    {
        $this->center = $center;

        return $this;
    }

    /**
     * Get center
     *
     * @return \Blog\CoreBundle\Entity\Center 
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return Upload
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
