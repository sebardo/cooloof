<?php

namespace Blog\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Blog\CoreBundle\Entity\User;

class LoginController extends BaseFrontController
{
    /**
     * @Route("/login", name="login")
     */

    public function loginAction(Request $request)
    {
        
        // whatever *your* User object is
//        $em = $this->getDoctrine()->getManager();
//        $user = $em->getRepository('CoreBundle:User')->find(1);
//        $plainPassword = 'admin';
//        $encoder = $this->container->get('security.password_encoder');
//        $encoded = $encoder->encodePassword($user, $plainPassword);
        //print_r($encoded);die;8919058ed1b22e260c8921f237becf0037bd057c

        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render("FrontBundle:Login:{$this->getTemplate()}/login.html.twig", array('last_username' => $lastUsername, 'error' => $error));
    }
}
