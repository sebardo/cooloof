<?php

namespace Blog\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostImage
 *
 * @ORM\Table(name="post_image")
 * @ORM\Entity
 */
class PostImage
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
     * @var \Blog\CoreBundle\Entity\Post $post
     *
     * @ORM\ManyToOne(targetEntity="\Blog\CoreBundle\Entity\Post", inversedBy="images")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $post;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * @return PostImage
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
     * Set post
     *
     * @param \Blog\CoreBundle\Entity\Post $post
     * @return PostImage
     */
    public function setPost(\Blog\CoreBundle\Entity\Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Blog\CoreBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }
}
