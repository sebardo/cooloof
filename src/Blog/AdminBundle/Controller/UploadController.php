<?php

namespace Blog\AdminBundle\Controller;

use Blog\CoreBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Blog\AdminBundle\Form\UploadType;
use Blog\CoreBundle\Entity\Upload;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/uploads")
 */

class UploadController extends BaseAdminController
{
    const DEFAULT_LOCALE = 'es';

    /**
     * @Route("/", name="upload_list")
     */

    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \Blog\CoreBundle\Repository\UploadRepository $uploadRepository */
        $uploadRepository = $em->getRepository('CoreBundle:Upload');

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $uploadRepository->findForPaginator($this->getUser(), $request->get('query'))->getQuery(),
            $request->isMethod('POST') ? 1 : $request->query->get('page', 1),
            10
        );

        return $this->render("AdminBundle:Upload:{$this->getTemplate()}/list.html.twig", array('pagination' => $pagination));
    }

    /**
     * @Route("/new", name="upload_new")
     */

    public function newAction(Request $request)
    {
        /** @var \Blog\CoreBundle\Entity\Upload $upload */
        $upload = new Upload();
        $upload->setEditId(sprintf('%09d', mt_rand(0, 1999999999)));

        $form = $this->createForm(new UploadType(), $upload, array('user' => $this->getUser()));
        $existingFiles = array();

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($upload);
                $em->flush();

                // Copy files: first the one with the default locale (ES)
                $root = $this->get('kernel')->getRootDir() . '/../';

                mkdir($root . Upload::UPLOAD_WEB_DIR . $upload->getId() . '/' . self::DEFAULT_LOCALE, 0777, true);
                $oldFile = $root . Upload::UPLOAD_TMP_WEB_DIR . $upload->getEditId() . '/' . self::DEFAULT_LOCALE . '/' . $upload->getFile();
                $newFile = $root . Upload::UPLOAD_WEB_DIR . $upload->getId() . '/' . self::DEFAULT_LOCALE . '/' . $upload->getFile();

                rename($oldFile, $newFile);

                // Copy files: translations
                /** @var \Blog\CoreBundle\Entity\UploadTranslation $translation */
                foreach ($upload->getTranslations() as $translation)
                {
                    if ($translation->getField() == 'file' && $translation->getContent())
                    {
                        mkdir($root . Upload::UPLOAD_WEB_DIR . $upload->getId() . '/' . $translation->getLocale(), 0777, true);

                        $oldFile = $root . Upload::UPLOAD_TMP_WEB_DIR . $upload->getEditId() . '/' . $translation->getLocale() . '/' . $translation->getContent();
                        $newFile = $root . Upload::UPLOAD_WEB_DIR . $upload->getId() . '/' . $translation->getLocale() . '/' . $translation->getContent();

                        rename($oldFile, $newFile);
                    }
                }

                // Copy files: delete tmp folder
                $this->deleteFolder($root . Upload::UPLOAD_TMP_WEB_DIR . $upload->getEditId());

                $translator = $this->get('translator');
                $this->get('session')->getFlashBag()->add('info', $translator->trans('labels.create_ok'));

                return $this->redirect($this->generateUrl('upload_list'));
            }
        }

        return $this->render("AdminBundle:Upload:{$this->getTemplate()}/edit.html.twig",
            array('form' => $form->createView(),
                'upload' => $upload,
                'existingFiles' => $existingFiles)
        );
    }

    /**
     * @Route("/{upload}/edit", name="upload_edit")
     */

    public function editAction(Request $request, Upload $upload)
    {
        $form = $this->createForm(new UploadType(), $upload, array('user' => $this->getUser()));
        $existingFiles = array();

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($upload);
                $em->flush();

                $translator = $this->get('translator');
                $this->get('session')->getFlashBag()->add('info', $translator->trans('labels.update_ok'));

                return $this->redirect($this->generateUrl('upload_list'));
            }
        }

        return $this->render("AdminBundle:Upload:{$this->getTemplate()}/edit.html.twig",
            array('form' => $form->createView(),
                'upload' => $upload,
                'existingFiles' => $existingFiles)
        );
    }


    /**
     * @Route("/{upload}/delete", name="upload_delete")
     */

    public function deleteAction(Request $request, Upload $upload)
    {
        $em = $this->getDoctrine()->getManager();

        $translator = $this->get('translator');

        try
        {
            $id = $upload->getId();

            $em->remove($upload);
            $em->flush();

            $this->deleteFolder($this->get('kernel')->getRootDir() . '/../' . Upload::UPLOAD_WEB_DIR . $id);

            $this->get('session')->getFlashBag()->add('info', $translator->trans('labels.delete_ok'));
        }
        catch (DBALException $e)
        {
            $request->getSession()->getFlashBag()->add('error', $translator->trans('upload.delete_ko'));
        }

        return $this->redirect($this->generateUrl('upload_list'));
    }

    /**
     * @Route("/upload-file", name="admin_upload_file")
     */

    public function uploadFileAction(Request $request)
    {
        $editId = $request->get('editId', null);
        $id = $request->get('id', null);
        $locale = $request->get('locale', null);
        $file = $request->files->get('file', null);

        $result['status'] = 'KO';

        if ($locale && $file && ($editId || $id))
        {
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $fileName = Urlizer::urlize(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;
            $root = $this->get('kernel')->getRootDir() . '/../';
            $path = null;

            if ($editId)
            {
                if (!preg_match('/^\d+$/', $editId)) {
                    throw new \Exception("Bad edit id");
                }

                $path = $root . Upload::UPLOAD_TMP_WEB_DIR . $editId . '/' . $locale;
            }
            elseif ($id)
            {
                if (!preg_match('/^\d+$/', $id)) {
                    throw new \Exception("Bad id");
                }

                $path = $root . Upload::UPLOAD_WEB_DIR . $id . '/' . $locale;
            }

            if ($file->move($path, $fileName))
            {
                $result['status'] = 'OK';
                $result['file'] = $fileName;
                $result['locale'] = $locale;
                $result['content'] = $this->get('templating')->render("AdminBundle:Upload:{$this->getTemplate()}/_file.html.twig", array('data' => $result));

                if ($id)
                {
                    // Update entity.
                    $em = $this->getDoctrine()->getManager();

                    /** @var \Blog\CoreBundle\Repository\UploadRepository $uploadRepository */
                    $uploadRepository = $em->getRepository('CoreBundle:Upload');

                    /** @var \Blog\CoreBundle\Entity\Upload $entity */
                    $entity = $uploadRepository->find($id);

                    if ($id)
                    {
                        $found = false;
                        if ($locale == self::DEFAULT_LOCALE) {
                            $found = true;
                            $entity->setFile($fileName);
                        }

                        if (!$found)
                        {
                            /** @var \Blog\CoreBundle\Entity\UploadTranslation $translation */
                            foreach ($entity->getTranslations() as $translation)
                            {
                                if ($translation->getField() == 'file' && $translation->getLocale() == $locale)
                                {
                                    $translation->setContent($fileName);
                                    break;
                                }
                            }
                        }

                        $em->persist($entity);
                        $em->flush();
                    }
                }
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/delete-file", name="admin_upload_delete_file")
     */

    public function deleteFileAction(Request $request)
    {
        $editId = $request->get('editId', null);
        $id = $request->get('id', null);
        $locale = $request->get('locale', null);
        $fileName = $request->get('fileName', null);

        $result['status'] = 'KO';

        if ($locale && $fileName && ($editId || $id))
        {
            $root = $this->get('kernel')->getRootDir() . '/../';
            $path = null;

            if ($editId)
            {
                if (!preg_match('/^\d+$/', $editId)) {
                    throw new \Exception("Bad edit id");
                }

                $path = $root . Upload::UPLOAD_TMP_WEB_DIR . $editId . '/' . $locale;
            }
            elseif ($id)
            {
                if (!preg_match('/^\d+$/', $id)) {
                    throw new \Exception("Bad id");
                }

                $path = $root . Upload::UPLOAD_WEB_DIR . $id . '/' . $locale;
            }

            if (file_exists($path . '/' . $fileName)) {
                if (unlink($path . '/' . $fileName)) {
                    $result['status'] = 'OK';
                }
            }

            if ($id)
            {
                // Update entity.
                $em = $this->getDoctrine()->getManager();

                /** @var \Blog\CoreBundle\Repository\UploadRepository $uploadRepository */
                $uploadRepository = $em->getRepository('CoreBundle:Upload');

                /** @var \Blog\CoreBundle\Entity\Upload $entity */
                $entity = $uploadRepository->find($id);

                if ($id)
                {
                    $found = false;
                    if ($entity->getFile() == $fileName && $locale == self::DEFAULT_LOCALE) {
                        $found = true;
                        $entity->setFile(null);
                    }

                    if (!$found)
                    {
                        /** @var \Blog\CoreBundle\Entity\UploadTranslation $translation */
                        foreach ($entity->getTranslations() as $translation)
                        {
                            if ($translation->getField() == 'file' && $translation->getLocale() == $locale)
                            {
                                $translation->setContent(null);
                                break;
                            }
                        }
                    }

                    $em->persist($entity);
                    $em->flush();
                }
            }
        }

        return new JsonResponse($result);
    }

    /********* PRIVATE FUNCTIONS ***************/

    private function deleteFolder($path)
    {
        if (is_dir($path) === true)
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file)
            {
                if (in_array($file->getBasename(), array('.', '..')) !== true)
                {
                    if ($file->isDir() === true)
                    {
                        rmdir($file->getPathName());
                    }

                    else if (($file->isFile() === true) || ($file->isLink() === true))
                    {
                        unlink($file->getPathname());
                    }
                }
            }

            return rmdir($path);
        }

        else if ((is_file($path) === true) || (is_link($path) === true))
        {
            return unlink($path);
        }

        return false;
    }
}
