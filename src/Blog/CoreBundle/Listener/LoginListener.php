<?php

namespace Blog\CoreBundle\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
   
class LoginListener
{
	private $context, $router, $isLogin;
	
	public function __construct(SecurityContext $context, Router $router)
	{
		$this->context = $context;
		$this->router = $router;
	}
	
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		$token = $event->getAuthenticationToken();
		$session = $event->getRequest()->getSession();
		
		if (property_exists($token->getUser(), 'lang')) {
			$session->set('_locale', $token->getUser()->getLang());
		}
		else {
			$session->set('_locale', 'es');
		}
		
		$this->isLogin = true;
	}
	
	public function onKernelResponse(FilterResponseEvent $event)
	{
		if ($this->isLogin != null && $this->isLogin == true)
		{
            if ($this->context->isGranted('ROLE_ADMIN')) {
                $page = $this->router->generate('admin_post_list');
            }
            elseif ($this->context->isGranted('ROLE_CENTER')) {
				$page = $this->router->generate('admin_post_list');
			}
            elseif ($this->context->isGranted('ROLE_PARENT')) {
                $page = $this->router->generate('blog_list');
            }
            else {
                $page = $this->router->generate('homepage');
            }

			$event->setResponse(new RedirectResponse($page));
			$event->stopPropagation();
		}
	}
}
