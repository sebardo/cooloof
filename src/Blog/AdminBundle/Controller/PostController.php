<?php

namespace Blog\AdminBundle\Controller;

use Blog\CoreBundle\Entity\Post;
use Blog\CoreBundle\Entity\PostImage;
use Blog\CoreBundle\Entity\PostTranslation;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/posts")
 */

class PostController extends BaseAdminController
{
    /**
     * @Route("/", name="admin_post_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Blog\CoreBundle\Repository\PostRepository $blogPostRespository */
        $blogPostRespository = $em->getRepository('CoreBundle:Post');

        $query = $blogPostRespository->getPaginationQuery($this->getUser(), $request->get('query'));

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );

        return $this->render("AdminBundle:Post:{$this->getTemplate()}/list.html.twig", array('pagination' => $pagination));
    }

    /**
     * @Route("/new", name="admin_post_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $post->setEditId(sprintf('%09d', mt_rand(0, 1999999999)));

        $form = $this->createForm($this->get('form.type.post'), $post);
        $existingGalleryFiles = array();
        $existingMainImageFiles = array();

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            /** @var \PunkAve\FileUploaderBundle\Services\FileUploader $fileUploader */
            $fileUploader = $this->get('punk_ave.file_uploader');

            if ($form->isValid())
            {
                $this->createSlugs($post);
                $post->setUser($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                $fileUploader->syncFiles(
                    array('from_folder' => Post::UPLOAD_MAIN_TMP_DIR . $post->getEditId(),
                        'to_folder' => Post::UPLOAD_MAIN_DIR . $post->getId(),
                        'remove_from_folder' => true,
                        'create_to_folder' => true));

                $files = $fileUploader->getFiles(array('folder' => Post::UPLOAD_MAIN_DIR . $post->getId(), 'subfolder' => 'large'));

                if ($files) {
                    $post->setImage($files[0]);
                    $em->persist($post);
                    $em->flush();
                }

                $fileUploader->syncFiles(
                    array('from_folder' => Post::UPLOAD_TMP_DIR . $post->getEditId(),
                        'to_folder' => Post::UPLOAD_DIR . $post->getId(),
                        'remove_from_folder' => true,
                        'create_to_folder' => true));


                $files = $fileUploader->getFiles(array('folder' => Post::UPLOAD_DIR . $post->getId(), 'subfolder' => 'large'));

                foreach ($files as $file)
                {
                    $postImage = new PostImage();
                    $postImage->setPost($post);
                    $postImage->setName($file);

                    $post->addImage($postImage);
                }

                $em->persist($post);
                $em->flush();

                $translator = $this->get('translator');
                $request->getSession()->getFlashBag()->add('success', $translator->trans('labels.create_ok'));

                return $this->redirect($this->generateUrl('admin_post_edit', array('id' => $post->getId())));
            }
            else {
                $existingGalleryFiles = $fileUploader->getFiles(array('folder' => Post::UPLOAD_TMP_DIR . $post->getEditId(), 'subfolder' => 'large'));
                $existingMainImageFiles = $fileUploader->getFiles(array('folder' => Post::UPLOAD_MAIN_TMP_DIR . $post->getEditId(), 'subfolder' => 'large'));
            }
        }

        return $this->render("AdminBundle:Post:{$this->getTemplate()}/edit.html.twig", array('post' => $post, 'form' => $form->createView(), 'existingFiles' => $existingGalleryFiles, 'existingMainImageFiles' => $existingMainImageFiles));
    }

    /**
     * @Route("/{id}/edit", name="admin_post_edit")
     * @Template()
     */
    public function editAction(Request $request, Post $post)
    {
        $post->setEditId(sprintf('%09d', mt_rand(0, 1999999999)));
        $form = $this->createForm($this->get('form.type.post'), $post);
        $existingGalleryFiles = array();
        $existingMainImageFiles = array();

        if ($request->getMethod() == 'POST') {

            /** @var \PunkAve\FileUploaderBundle\Services\FileUploader $fileUploader */
            $fileUploader = $this->get('punk_ave.file_uploader');
            $form->handleRequest($request);
            $existingGalleryFiles = $fileUploader->getFiles(array('folder' => Post::UPLOAD_TMP_DIR . $post->getEditId(), 'subfolder' => 'large'));
            $existingMainImageFiles = $fileUploader->getFiles(array('folder' => Post::UPLOAD_MAIN_TMP_DIR . $post->getEditId(), 'subfolder' => 'large'));

            if ($form->isValid())
            {
                $fileUploader->syncFiles(
                    array('from_folder' => Post::UPLOAD_TMP_DIR . $post->getEditId(),
                        'to_folder' => Post::UPLOAD_DIR . $post->getId(),
                        'remove_from_folder' => true,
                        'create_to_folder' => true));

                $files = $fileUploader->getFiles(array('folder' => Post::UPLOAD_DIR . $post->getId(), 'subfolder' => 'large'));

                $post->getImages()->clear();

                foreach ($files as $file)
                {
                    $postImage = new PostImage();
                    $postImage->setPost($post);
                    $postImage->setName($file);

                    $post->addImage($postImage);
                }

                $this->createSlugs($post);

                $fileUploader->syncFiles(
                    array('from_folder' => Post::UPLOAD_MAIN_TMP_DIR . $post->getEditId(),
                        'to_folder' => Post::UPLOAD_MAIN_DIR . $post->getId(),
                        'remove_from_folder' => true,
                        'create_to_folder' => true));

                $files = $fileUploader->getFiles(array('folder' => Post::UPLOAD_MAIN_DIR . $post->getId(), 'subfolder' => 'large'));

                if ($files) {
                    $post->setImage($files[0]);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                $translator = $this->get('translator');
                $request->getSession()->getFlashBag()->add('success', $translator->trans('labels.update_ok'));

                return $this->redirect($this->generateUrl('admin_post_edit', array('id' => $post->getId())));
            }
        }

        return $this->render("AdminBundle:Post:{$this->getTemplate()}/edit.html.twig", array('post' => $post, 'form' => $form->createView(), 'existingFiles' => $existingGalleryFiles, 'existingMainImageFiles' => $existingMainImageFiles));
    }

    /**
     * @Route("/{id}/delete", name="admin_post_delete")
     */

    public function deleteAction(Request $request, Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $post->getId();

        $em->remove($post);
        $em->flush();

        /** @var \Blog\CoreBundle\Service\UtilService $utilService */
        $utilService = $this->get('util.service');

        // Delete resources
        if ($post->getImage()) {
            $folder = $utilService->normalizePath($this->get('kernel')->getRootDir() . '/../web/uploads/' . Post::UPLOAD_MAIN_DIR);
            $utilService->rrmdir($folder . $id);
        }

        if (count($post->getImages())) {
            $folder = $utilService->normalizePath($this->get('kernel')->getRootDir() . '/../web/uploads/' . Post::UPLOAD_DIR);
            $utilService->rrmdir($folder . $id);
        }

        $translator = $this->get('translator');
        $request->getSession()->getFlashBag()->add('success', $translator->trans('labels.delete_ok'));

        return $this->redirect($this->generateUrl('admin_post_list'));
    }

    /**
     *
     * @Route("/upload-gallery", name="admin_post_upload_gallery_images")
     * @Template()
     */
    public function uploadGalleryImagesAction(Request $request)
    {
        /** @var \Blog\CoreBundle\Service\FileUploader\FileUploader $fileUploader */
        $fileUploader = $this->get('punk_ave.file_uploader');

        $editId = $request->get('editId', null);
        if ($editId) {
            if (!preg_match('/^\d+$/', $editId)) {
                throw new \Exception("Bad edit id");
            }

            $parameters = array();
            $parameters['sizes'] = array();
            $parameters['editId'] = $editId;
            $parameters['folder'] = substr(Post::UPLOAD_TMP_DIR, 2) . $editId;
            $parameters['sizes'] = array('thumbnail' => array('folder' => 'thumbnails', 'max_width' => 320, 'max_height' => 480), 'large' => array('folder' => 'large', 'max_width' => 1200, 'max_height' => 3000));
            $parameters['allowed_extensions'] = array('gif', 'png', 'jpg', 'jpeg');
            $parameters['param_name'] = 'images';
            $parameters['type'] = 'gallery';
            $parameters['delete_original_file'] = true;

            $fileUploader->handleFileUpload($parameters);
        }
        else {
            $id = $request->get('id', null);

            if (!preg_match('/^\d+$/', $id)) {
                throw new \Exception("Bad id");
            }

            if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
            {
                $em = $this->getDoctrine()->getManager();

                $postImageRepository = $em->getRepository('CoreBundle:PostImage');
                $documentImage = $postImageRepository->findOneBy(array('post' => $id, 'name' => $request->get('file')));
                if ($documentImage) {
                    $em->remove($documentImage);
                    $em->flush();
                }
            }

            $folder = substr(Post::UPLOAD_DIR, 1) . $id;
            $fileUploader->handleFileUpload(array('folder' => $folder));
        }
    }

    /**
     *
     * @Route("/upload-main", name="admin_post_upload_main_image")
     * @Template()
     */
    public function uploadMainImageAction(Request $request)
    {
        /** @var \Blog\CoreBundle\Service\FileUploader\FileUploader $fileUploader */
        $fileUploader = $this->get('punk_ave.file_uploader');

        $editId = $request->get('editId', null);
        if ($editId) {
            if (!preg_match('/^\d+$/', $editId)) {
                throw new \Exception("Bad edit id");
            }

            $parameters = array();
            $parameters['sizes'] = array();
            $parameters['editId'] = $editId;
            $parameters['folder'] = substr(Post::UPLOAD_MAIN_TMP_DIR, 2) . $editId;
            $parameters['allowed_extensions'] = array('gif', 'png', 'jpg', 'jpeg');
            $parameters['min_width'] = 820;
            $parameters['sizes'] = array('thumbnail' => array('folder' => 'thumbnails', 'max_width' => 400, 'max_height' => 1200), 'large' => array('folder' => 'large', 'max_width' => 820, 'max_height' => 3000));
            $parameters['param_name'] = 'image';
            $parameters['max_number_of_files'] = 1;
            $parameters['type'] = 'main';
            $parameters['delete_original_file'] = true;

            $fileUploader->handleFileUpload($parameters);
        }
        else {
            $id = $request->get('id', null);

            if (!preg_match('/^\d+$/', $id)) {
                throw new \Exception("Bad id");
            }

            if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
            {
                $em = $this->getDoctrine()->getManager();

                $postRepository = $em->getRepository('CoreBundle:Post');
                $post = $postRepository->find($id);
                if ($post) {
                    $post->setImage(null);
                    $em->persist($post);
                    $em->flush();
                }
            }

            $folder = substr(Post::UPLOAD_MAIN_DIR, 1) . $id;
            $fileUploader->handleFileUpload(array('folder' => $folder));
        }
    }

    /**
     *
     * @Route("/{id}/duplicate", name="admin_post_duplicate")
     * @Template()
     */
    public function duplicateAction(Request $request, Post $post)
    {
        $em = $this->getDoctrine()->getManager();

        $newPost = new Post();
        $newPost->setTitle($post->getTitle());
        $newPost->setUser($post->getUser());
        $newPost->setWeek($post->getWeek());
        $newPost->setIntroduction($post->getIntroduction());
        $newPost->setContent($post->getContent());
        $newPost->setDate($post->getDate());
        $newPost->setImage($post->getImage());

        /** @var \Blog\CoreBundle\Entity\PostTranslation $translation */
        $translation = null;
        foreach ($post->getTranslations() as $translation)
        {
            $postTranslation = new PostTranslation();
            $postTranslation->setContent($translation->getContent());
            $postTranslation->setField($translation->getField());
            $postTranslation->setLocale($translation->getLocale());
            $postTranslation->setObject($newPost);

            $newPost->addTranslation($postTranslation);
        }

        $slug = $post->getSlug();
        $slugOk = false;
        $cont = 1;
        do
        {
            $suffix = '-' . $cont;
            /** @var \Blog\CoreBundle\Repository\PostRepository $blogPostRespository */
            $postRespository = $em->getRepository('CoreBundle:Post');

            $p = $postRespository->findOneBy(array('slug' => $slug . $suffix));
            if (!$p) {
                $slugOk = true;
                $newPost->setSlug($slug . $suffix);
            }

            $cont++;

        } while (!$slugOk);


        $this->createSlugs($newPost);

        $em->persist($newPost);
        $em->flush();

        /** @var \Blog\CoreBundle\Service\UtilService $utilService */
        $utilService = $this->get('util.service');
        $sizes = array('originals', 'thumbnails', 'large');
        $folder = $utilService->normalizePath($this->get('kernel')->getRootDir() . '/../web/uploads/' . Post::UPLOAD_MAIN_DIR);

        if ($post->getImage())
        {
            foreach ($sizes as $size) {
                if (file_exists($folder . $post->getId() . "/$size/" . $post->getImage())) {
                    if (!file_exists($folder . $newPost->getId() . "/$size")) {
                        mkdir($folder . $newPost->getId() . "/$size", 0777, true);
                    }
                    copy($folder . $post->getId() . "/$size/" . $post->getImage(), $folder . $newPost->getId() . "/$size/" . $post->getImage());
                }
            }
        }


        $folder = $utilService->normalizePath($this->get('kernel')->getRootDir() . '/../web/uploads/' . Post::UPLOAD_DIR);
        /** @var \Blog\CoreBundle\Entity\PostImage $image */
        $image = null;
        foreach ($post->getImages() as $image)
        {
            $newImage = new PostImage();
            $newImage->setName($image->getName());
            $newImage->setPost($newPost);

            $newPost->addImage($newImage);

            foreach ($sizes as $size)
            {
                if (file_exists($folder . $post->getId() . "/$size/" . $image->getName()))
                {
                    if (!file_exists($folder . $newPost->getId() . "/$size")) {
                        mkdir($folder . $newPost->getId() . "/$size", 0777, true);
                    }
                    copy($folder . $post->getId() . "/$size/" . $image->getName(), $folder . $newPost->getId() . "/$size/" . $image->getName());
                }
            }
        }

        $em->persist($newPost);
        $em->flush();

        $translator = $this->get('translator');
        $request->getSession()->getFlashBag()->add('success', $translator->trans('post.duplicated_ok'));

        return $this->redirect($this->generateUrl('admin_post_edit', array('id' => $newPost->getId())));
    }

    /**
     *
     * @Route("/upload-content", name="admin_post_upload_content_image")
     */
    public function uploadContentImageAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException('Invalid request');
        }

        $response['status'] = 'KO';

        $file = $request->files->get('file');

        if ($file != null)
        {
            $folder = $this->get('kernel')->getRootDir() . '/../web' . Post::UPLOAD_CONTENT_DIR;

            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $imageName = uniqid(Urlizer::urlize($name) . '-');

            if ($file->move($folder, $imageName . '.' . $extension))
            {
                /** @var \Blog\CoreBundle\Service\UtilService $utilService */
                $utilService = $this->get('util.service');
                $options['upload_dir'] = $folder;
                $options['max_width'] = 820;
                $options['max_height'] = 600;

                if ($utilService->createScaledImage($imageName . '.' . $extension, $options)) {
                    $response['fullPath'] = Post::UPLOAD_CONTENT_DIR . $imageName . '.' . $extension;
                    $response['status'] = 'OK';
                }
            }
         /*
            if ($file->getClientSize() > Post::UPLOAD_CONTENT_MAX_SIZE) {
                $translator = $this->get('translator');
                $response['message'] = $translator->trans('post.upload_file_size_restriction', array('%size%' => (Post::UPLOAD_CONTENT_MAX_SIZE / 1024 / 1024) . 'MB'));
            }
         */
        }

        return new JsonResponse($response);
    }

    private function createSlugs(Post &$entity)
    {
        $em = $this->getDoctrine()->getManager();

        $languages = array('ca');

        foreach ($languages as $lang)
        {
            $titleTranslation = $entity->getFieldByLocale('title', $lang);
            $slugTranslation = $entity->getFieldByLocale('slug', $lang);

            if ($titleTranslation && $titleTranslation->getContent())
            {
                $slugOk = false;
                $suffix = '';
                $cont = 0;
                do
                {
                    if ($cont > 0) {
                        $suffix = '-' . $cont;
                    }

                    if (!$slugTranslation->getContent()) {
                        $slugTranslation->setContent($entity->createSlug($titleTranslation->getContent()) . $suffix);
                    }
                    else {
                        $slugTranslation->setContent($entity->createSlug($slugTranslation->getContent()) . $suffix);
                    }

                    $postTranslationRepo = $em->getRepository('CoreBundle:PostTranslation');
                    $translation = $postTranslationRepo->findOneBy(array('field' => 'slug', 'content' => $slugTranslation->getContent()));
                    if (!$translation || $translation->getId() == $slugTranslation->getId()) {
                        $slugOk = true;
                    }

                    $cont++;

                } while (!$slugOk);
            }
        }
    }
}
