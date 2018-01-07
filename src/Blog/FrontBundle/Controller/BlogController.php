<?php

namespace Blog\FrontBundle\Controller;

use Blog\CoreBundle\Entity\PostComment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlogController extends BaseFrontController
{
    /**
     * @Route("/", name="blog_list")
     */

    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostRepository $blogPostRespository */
        $blogPostRespository = $em->getRepository('CoreBundle:Post');

        $query = $blogPostRespository->getPaginationQueryForBlog($this->getUser(), $request->get('query'));

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            5
        );

        return $this->render("FrontBundle:Blog:{$this->getTemplate()}/list.html.twig", array('pagination' => $pagination, 'uploads' => $this->getUploads()));
    }

    /**
     * @Route("/{slug}", name="blog_detail")
     */

    public function detailAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostRepository $blogPostRespository */
        $blogPostRespository = $em->getRepository('CoreBundle:Post');

        $post = $blogPostRespository->findBySlugAndUser($slug, $this->getUser(), true);

        if (!$post) {
            throw $this->createNotFoundException('Post does not exist or user has no rights');
        }

        $form = $this->createFormBuilder()->setMethod('POST')
            ->add('name', 'text', array(
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'constraints' => array(new NotBlank()),
                'label_attr' => array('class' => 'control-label'),
                'label' => 'post.name'))
            ->add('comment', 'textarea', array(
                'required' => true,
                'attr' => array('class' => 'form-control'),
                'constraints' => array(new NotBlank()),
                'label' => 'post.comment',
                'label_attr' => array('class' => 'control-label')))
        ;

        $form = $form->getForm();

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);
            $data = $form->getData();

            if ($form->isValid())
            {
                $postComment = new PostComment();
                $postComment->setPost($post);
                $postComment->setName($data['name']);
                $postComment->setComment($data['comment']);

                $em->persist($postComment);
                $em->flush();

                $translator = $this->get('translator');
                $request->getSession()->getFlashBag()->add('success', $translator->trans('post.comment_sent_ok'));

                return $this->redirect($this->generateUrl('blog_detail', array('slug' => $post->getSlug())));
            }
        }

        return $this->render("FrontBundle:Blog:{$this->getTemplate()}/detail.html.twig", array('post' => $post, 'form' => $form->createView(), 'uploads' => $this->getUploads()));
    }

    private function getUploads()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\UploadRepository $uploadRespository */
        $uploadRespository = $em->getRepository('CoreBundle:Upload');

        $uploads = $uploadRespository->findUploadsByUser($this->getUser());

        for ($i = 0; $i < count($uploads); $i++) {
            if (!$uploads[$i]->getFile()) {
                unset($uploads[$i]);
            }
        }

        return $uploads;
    }
}
