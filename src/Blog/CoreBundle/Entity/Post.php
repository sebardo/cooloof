<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @Assert\Callback(methods={"validate"})
 * @ORM\Entity(repositoryClass="Blog\CoreBundle\Repository\PostRepository")
 * @Gedmo\TranslationEntity(class="Blog\CoreBundle\Entity\PostTranslation")
 */

class Post
{
    const UPLOAD_DIR = './../../uploads/assets/images/';
    const UPLOAD_TMP_DIR = './../../uploads/tmp/assets/images/';

    const UPLOAD_MAIN_DIR = './../../uploads/assets/images/main/';
    const UPLOAD_MAIN_TMP_DIR = './../../uploads/tmp/assets/images/main/';

    const UPLOAD_CONTENT_DIR = '/uploads/posts/';
    const UPLOAD_CONTENT_MAX_SIZE = 1048576; // 1MB

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Blog\CoreBundle\Entity\Week $week
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="\Blog\CoreBundle\Entity\Week", inversedBy="posts")
     * @ORM\JoinColumn(name="week_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $week;

    /**
     * @var \Blog\CoreBundle\Entity\User $user
     *
     * @ORM\ManyToOne(targetEntity="\Blog\CoreBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    private $introduction;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    private $content;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

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
     * @ORM\OneToMany(
     *   targetEntity="Blog\CoreBundle\Entity\PostTranslation",
     *   mappedBy="object",
     *   cascade={"all"}
     * )
     * @Assert\Valid
     */
    private $translations;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $images
     *
     * @ORM\OneToMany(targetEntity="\Blog\CoreBundle\Entity\PostImage", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $images;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $images
     *
     * @ORM\OneToMany(targetEntity="\Blog\CoreBundle\Entity\PostComment", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $comments;

    /**
     * @var integer
     *
     * @Assert\Regex("/^\d+$/")
     */
    private $editId;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
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
     * Set title
     *
     * @param string $title
     * @return Post
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
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Post
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Post
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Post
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
     * Set active
     *
     * @param boolean $active
     * @return Post
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

    /**
     * Set slug
     *
     * @param string $slug
     * @return BlogPost
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add translations
     *
     * @param \Blog\CoreBundle\Entity\PostTranslation $translations
     * @return BlogPost
     */
    public function addTranslation(\Blog\CoreBundle\Entity\PostTranslation $translations)
    {
        $translations->setObject($this);

        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Blog\CoreBundle\Entity\PostTranslation $translations
     */
    public function removeTranslation(\Blog\CoreBundle\Entity\PostTranslation $translations)
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

    public function getFieldByLocale($field, $locale)
    {
        foreach ($this->translations as $translation)
        {
            if ($translation->getField() == $field && $translation->getLocale() == $locale) {
                return $translation;
            }
        }

        return null;
    }

    public function createSlug($title)
    {
        $slug = null;
        if ($title) {
            $slug = Urlizer::urlize($title, '-');
        }

        return $slug;
    }

    public function validate(ExecutionContextInterface $context)
    {
        /*
        $languages = array('ca', 'en', 'nl', 'eu', 'pt', 'fr', 'it', 'cs');

        foreach ($languages as $lang)
        {
            $titleTranslation = $this->getFieldByLocale('title', $lang);
            $slugTranslation = $this->getFieldByLocale('slug', $lang);

            if ($slugTranslation && $titleTranslation && $slugTranslation->getContent()) {
                if (!Urlizer::validUtf8($slugTranslation->getContent())) {
                    $context->addViolationAt(
                        'slug',
                        'This name sounds totally fake!'
                    );
                }
            }
        }
        */
    }

    /**
     * Add images
     *
     * @param \Blog\CoreBundle\Entity\PostImage $images
     * @return Post
     */
    public function addImage(\Blog\CoreBundle\Entity\PostImage $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Blog\CoreBundle\Entity\PostImage $images
     */
    public function removeImage(\Blog\CoreBundle\Entity\PostImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
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
     * @return Post
     */
    public function setEditId($editId)
    {
        $this->editId = $editId;

        return $this;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Post
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
     * @return Post
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
     * Set week
     *
     * @param \Blog\CoreBundle\Entity\Week $week
     * @return Post
     */
    public function setWeek(\Blog\CoreBundle\Entity\Week $week)
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

    /**
     * Set user
     *
     * @param \Blog\CoreBundle\Entity\User $user
     * @return Post
     */
    public function setUser(\Blog\CoreBundle\Entity\User $user)
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
     * Set introduction
     *
     * @param string $introduction
     * @return Post
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction
     *
     * @return string 
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Add comments
     *
     * @param \Blog\CoreBundle\Entity\PostComment $comments
     * @return Post
     */
    public function addComment(\Blog\CoreBundle\Entity\PostComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Blog\CoreBundle\Entity\PostComment $comments
     */
    public function removeComment(\Blog\CoreBundle\Entity\PostComment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function getShortTitle()
    {
        $title = $this->title;

        if (strlen($title) > 20) {
            $title = substr($title, 0, 17) . '...';
        }

        return $title;
    }
}
