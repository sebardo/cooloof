<?php

namespace Blog\AdminBundle\Controller;

use Blog\AdminBundle\Form\PostCommentType;
use Blog\CoreBundle\Entity\Post;
use Blog\CoreBundle\Entity\PostComment;
use Blog\CoreBundle\Entity\PostImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PostCommentController extends BaseAdminController
{
    /**
     * @Route("/comments", name="admin_post_comments_all_list")
     * @Template()
     */
    public function listAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

        $query = $postCommentRespository->getPaginationAllQuery($this->getUser(), $request->get('query'));

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );

        return $this->render("AdminBundle:PostComment:{$this->getTemplate()}/list.html.twig", array('pagination' => $pagination, 'post' => null));
    }

    /**
     * @Route("/post/{slug}/comments", name="admin_post_comments_list")
     * @Template()
     */
    public function listAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostRepository $blogPostRespository */
        $blogPostRespository = $em->getRepository('CoreBundle:Post');

        $post = $blogPostRespository->findBySlugAndUser($slug, $this->getUser());

        if (!$post) {
            throw $this->createNotFoundException('Post does not exist or user has no rights');
        }

        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

        $query = $postCommentRespository->getPaginationQuery($post, $request->get('query'));

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );

        return $this->render("AdminBundle:PostComment:{$this->getTemplate()}/list.html.twig", array('pagination' => $pagination, 'post' => $post));
    }

    /**
     * @Route("/comments/{commentId}/edit", name="admin_post_comment_all_edit")
     * @Template()
     */
    public function editAllAction(Request $request, $commentId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

        $comment = $postCommentRespository->findByIdAndUser($commentId, $this->getUser());

        if (!$comment) {
            throw $this->createNotFoundException('Comment does not exist or user has no rights');
        }

        $form = $this->createForm(new PostCommentType(), $comment);

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $translator = $this->get('translator');
                $request->getSession()->getFlashBag()->add('success', $translator->trans('labels.update_ok'));

                return $this->redirect($this->generateUrl('admin_post_comments_all_list'));
            }
        }

        return $this->render("AdminBundle:PostComment:{$this->getTemplate()}/edit.html.twig", array('comment' => $comment, 'form' => $form->createView(), 'all' => true));
    }

    /**
     * @Route("/post/{slug}/comments/{commentId}/edit", name="admin_post_comment_edit")
     * @Template()
     */
    public function editAction(Request $request, $slug, $commentId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

        $comment = $postCommentRespository->findByPostAndIdAndUser($slug, $commentId, $this->getUser());

        if (!$comment) {
            throw $this->createNotFoundException('Comment does not exist or user has no rights');
        }

        $form = $this->createForm(new PostCommentType(), $comment);

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $translator = $this->get('translator');
                $request->getSession()->getFlashBag()->add('success', $translator->trans('labels.update_ok'));

                return $this->redirect($this->generateUrl('admin_post_comments_list', array('slug' => $slug)));
            }
        }

        return $this->render("AdminBundle:PostComment:{$this->getTemplate()}/edit.html.twig", array('comment' => $comment, 'form' => $form->createView(), 'all' => false));
    }

    /**
     * @Route("/comments/{commentId}/delete", name="admin_post_comment_all_delete")
     */
    public function deleteAllAction(Request $request, $commentId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

        $comment = $postCommentRespository->findByIdAndUser($commentId, $this->getUser());

        if (!$comment) {
            throw $this->createNotFoundException('Comment does not exist or user has no rights');
        }

        $em->remove($comment);
        $em->flush();

        $translator = $this->get('translator');
        $request->getSession()->getFlashBag()->add('success', $translator->trans('labels.delete_ok'));

        return $this->redirect($this->generateUrl('admin_post_comments_all_list'));
    }

    /**
     * @Route("/post/{slug}/comments/{commentId}/delete", name="admin_post_comment_delete")
     */
    public function deleteAction(Request $request, $slug, $commentId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
        $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

        $comment = $postCommentRespository->findByPostAndIdAndUser($slug, $commentId, $this->getUser());

        if (!$comment) {
            throw $this->createNotFoundException('Comment does not exist or user has no rights');
        }

        $em->remove($comment);
        $em->flush();

        $translator = $this->get('translator');
        $request->getSession()->getFlashBag()->add('success', $translator->trans('labels.delete_ok'));

        return $this->redirect($this->generateUrl('admin_post_comments_list', array('slug' => $slug)));
    }

    /**
     * @Route("/post/{slug}/comments/validate-batch", name="admin_post_comment_validate_batch")
     */
    public function validateBatchAction(Request $request, $slug)
    {
        $cont = 0;
        $em = $this->getDoctrine()->getManager();
        $comments = $request->get('comments');

        if ($comments)
        {
            /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
            $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

            foreach ($comments as $commentId)
            {
                $comment = $postCommentRespository->findByIdAndUser($commentId, $this->getUser());

                if ($comment && !$comment->getValidated()) {
                    $comment->setValidated(true);
                    $em->persist($comment);
                    $cont++;
                }
            }
        }

        if ($cont) {
            $em->flush();
            $translator = $this->get('translator');
            $request->getSession()->getFlashBag()->add('success', $translator->trans('comment.validated_ok'));
        }

        return $this->redirect($this->generateUrl('admin_post_comments_list', array('slug' => $slug)));
    }

    /**
     * @Route("/comments/validate-batch", name="admin_post_comment_all_validate_batch")
     */
    public function validateBatchAllAction(Request $request)
    {
        $cont = 0;
        $em = $this->getDoctrine()->getManager();
        $comments = $request->get('comments');

        if ($comments)
        {
            /** @var \Blog\CoreBundle\Repository\PostCommentRepository $postCommentRespository */
            $postCommentRespository = $em->getRepository('CoreBundle:PostComment');

            foreach ($comments as $commentId)
            {
                $comment = $postCommentRespository->findByIdAndUser($commentId, $this->getUser());

                if ($comment && !$comment->getValidated()) {
                    $comment->setValidated(true);
                    $em->persist($comment);
                    $cont++;
                }
            }
        }

        if ($cont) {
            $em->flush();
            $translator = $this->get('translator');
            $request->getSession()->getFlashBag()->add('success', $translator->trans('comment.validated_ok'));
        }

        return $this->redirect($this->generateUrl('admin_post_comments_all_list'));
    }
}
