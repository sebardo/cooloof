<?php

namespace Blog\AdminBundle\Controller;

use Blog\CoreBundle\Entity\Post;
use Blog\CoreBundle\Entity\PostImage;
use Blog\CoreBundle\Entity\User;
use Blog\CoreBundle\Entity\UserProfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @Route("/weeks")
 */

class WeekController extends BaseAdminController
{
    /**
     * @Route("/", name="admin_week_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\WeekRepository $weekRespository */
        $weekRespository = $em->getRepository('CoreBundle:Week');

        $query = $weekRespository->getPaginationQuery($this->getUser());

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            15
        );

        return $this->render("AdminBundle:Week:{$this->getTemplate()}/list.html.twig", array('pagination' => $pagination));
    }

    /**
     * @Route("/{id}/edit-user", name="admin_week_edit_user")
     * @Template()
     */
    public function editUserAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');

        /** @var \Blog\CoreBundle\Repository\WeekRepository $weekRespository */
        $weekRespository = $em->getRepository('CoreBundle:Week');

        /** @var \Blog\CoreBundle\Entity\Week $week */
        $week = $weekRespository->findWeekByIdAndUser($id, $this->getUser());

        if (!$week) {
            throw $this->createNotFoundException('Week does not exist or user has no rights');
        }

        $form = $this->createFormBuilder()->setMethod('POST')
            ->add('username', 'text', array(
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'constraints' => array(new NotBlank(), new Regex(array('pattern' => "/^[a-zA-Z0-9_]+$/")), new Length(array('min' => 6))),
                'label_attr' => array('class' => 'control-label'),
                'label' => 'week.user_with_help',
                'data' => $week->getProfile() ? $week->getProfile()->getUser()->getUserName() : null))
            ->add('password', 'text', array(
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'constraints' => array(new NotBlank(), new Length(array('min' => 6))),
                'label' => 'week.password_with_help',
                'label_attr' => array('class' => 'control-label'),
                'data' => $week->getProfile() ? $week->getProfile()->getPlainPassword() : null))
            ->add('active', 'checkbox', array(
                'label' => 'labels.active',
                'data' => $week->getProfile() ? $week->getProfile()->getUser()->getActive() : true))
            ;


        $form->addEventListener(FormEvents::POST_SUBMIT, function ($event) use ($translator, $em, $week)
        {
            $data = $event->getData();
            $form = $event->getForm();

            if (!$week->getProfile())
            {
                // Comprobamos que no existe un usuario igual
                /** @var \Blog\CoreBundle\Repository\UserRepository $userRespository */
                $userRespository = $em->getRepository('CoreBundle:User');
                $user = $userRespository->findOneBy(array('userName' => $data['username']));
                if ($user) {
                    $form->get('username')->addError(new FormError($translator->trans('week.user_already_exists')));
                }
            }
        });

        $form = $form->getForm();

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);
            $data = $form->getData();

            if ($form->isValid())
            {
                if ($week->getProfile())
                {
                    $salt = md5(time());
                    $password = $data['password'];
                    $encoder = $this->get('security.encoder_factory')->getEncoder($week->getProfile()->getUser());
                    $week->getProfile()->getUser()->setPassword($encoder->encodePassword($password, $salt));
                    $week->getProfile()->getUser()->setSalt($salt);
                    $week->getProfile()->getUser()->setActive($data['active']);
                    $week->getProfile()->getUser()->setUserName($data['username']);

                    $week->getProfile()->setEmail($data['username']);
                    $week->getProfile()->setPlainPassword($data['password']);
                }
                else {
                    $user = new User();
                    $user->setUserName($data['username']);
                    $user->setLastLogin(new \DateTime());

                    $salt = md5(time());
                    $password = $data['password'];
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $user->setPassword($encoder->encodePassword($password, $salt));
                    $user->setSalt($salt);

                    $userProfile = new UserProfile();
                    $userProfile->setEmail($data['username']);
                    $userProfile->setPlainPassword($data['password']);
                    $userProfile->setUser($user);

                    $week->setProfile($userProfile);
                }

                $em->persist($week);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', $translator->trans('labels.update_ok'));

                return $this->redirect($this->generateUrl('admin_week_list'));
            }
        }

        return $this->render("AdminBundle:Week:{$this->getTemplate()}/editUser.html.twig", array('week' => $week, 'form' => $form->createView()));
    }
}
