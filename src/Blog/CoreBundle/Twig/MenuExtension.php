<?php

namespace Blog\CoreBundle\Twig;

use PEC\CoreBundle\Entity\Campo;
use PEC\CoreBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

class MenuExtension extends \Twig_Extension
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getStateMenuClass($template = 'smart')
    {
        $cookies = $this->requestStack->getCurrentRequest()->cookies;
        if ($cookies->has('menu-status'))
        {
            if ($cookies->get('menu-status') == 'hide') {
                return 'minified';
            }
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'get_state_menu_class' => new \Twig_Function_Method($this, 'getStateMenuClass'),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'menu_extension';
    }
}
