<?php

namespace Blog\AdminBundle\Twig;

use Blog\CoreBundle\Entity\User;
use PEC\CoreBundle\Entity\Campo;
use PEC\CoreBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UtilExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    protected $em;

    protected $requestStack;

    public function __construct($em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function renderNumComments(User $user)
    {
        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $this->em->getRepository('CoreBundle:PostComment');

        $comments = $postCommentRespository->findByUserAndValidated($user);

        if (count($comments)) {
            return '<span class="badge bg-color-redLight pull-right inbox-badge">' . count($comments) . '</span>';
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'render_num_comments' => new \Twig_Function_Method($this, 'renderNumComments', array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'util_extension';
    }
}
