<?php

namespace Blog\FrontBundle\Controller;

use Blog\CoreBundle\Entity\Post;
use Blog\CoreBundle\Entity\Upload;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/file")
 * @Security("has_role('ROLE_PARENT')")
 */

class FileController extends Controller
{
    /**
     * @Route("/serve/{id}/{folder}/{type}/{fileName}", name="serve_file", requirements={"folder" = "large|medium|originals|small|thumbnails", "type" = "main|gallery"}, options={"i18n"=false})
     */

    public function serveFileAction(Request $request, $id, $folder = 'large', $fileName = null, $type)
    {
        $fileFullPath = $this->get('kernel')->getRootDir() . '/../web/uploads/';

        if ($type == 'gallery') {
            $fileFullPath .= Post::UPLOAD_DIR . $id . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $fileName;
            /*
            if ($folder == 'large') {
                if (!file_exists($fileFullPath)) {
                    $fileFullPath = $this->get('kernel')->getRootDir() . '/../web/uploads/' . Post::UPLOAD_DIR . $id . DIRECTORY_SEPARATOR . 'originals' . DIRECTORY_SEPARATOR . $fileName;
                }
            }
            */
        }
        else {
            $fileFullPath .= Post::UPLOAD_MAIN_DIR . $id . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $fileName;
            /*
            if ($folder == 'large') {
                if (!file_exists($fileFullPath)) {
                    $fileFullPath = $this->get('kernel')->getRootDir() . '/../web/uploads/' . Post::UPLOAD_MAIN_DIR . $id . DIRECTORY_SEPARATOR . 'originals' . DIRECTORY_SEPARATOR . $fileName;
                }
            }
            */
        }

        /** @var \Blog\CoreBundle\Service\UtilService $utilService */
        $utilService = $this->get('util.service');

        return $this->getResponse($utilService->normalizePath($fileFullPath), $fileName);
    }

    /**
     * @Route("/tmp/serve/{editId}/{type}/{name}", name="serve_temp_file", requirements={"type" = "main|gallery"}, options={"i18n"=false})
     */

    public function serveTempFileAction(Request $request, $editId, $name, $type)
    {
        $fileFullPath = $this->get('kernel')->getRootDir() . '/../web/uploads/';
        if ($type == 'gallery') {
            $fileFullPath .= Post::UPLOAD_TMP_DIR . $editId . DIRECTORY_SEPARATOR . 'large' . DIRECTORY_SEPARATOR . $name;
        }
        else {
            $fileFullPath .= Post::UPLOAD_MAIN_TMP_DIR . $editId . DIRECTORY_SEPARATOR . 'large' . DIRECTORY_SEPARATOR . $name;
        }

        /** @var \Blog\CoreBundle\Service\UtilService $utilService */
        $utilService = $this->get('util.service');

        return $this->getResponse($utilService->normalizePath($fileFullPath), $name);
    }

    /**
     * @Route("/serve-upload/{folder}/{locale}/{fileName}/{tmp}", name="serve_upload_file", options={"i18n"=false, "expose"=true})
     */

    public function serveUploadAction(Request $request, $folder, $locale, $fileName, $tmp)
    {
        $fileFullPath = $this->get('kernel')->getRootDir() . '/../';

        if ($tmp == 1) {
            $fileFullPath .= Upload::UPLOAD_TMP_WEB_DIR . $folder . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $fileName;
        }
        else {
            $fileFullPath .= Upload::UPLOAD_WEB_DIR . $folder . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $fileName;
        }

        /** @var \Blog\CoreBundle\Service\UtilService $utilService */
        $utilService = $this->get('util.service');

        return $this->getResponse($utilService->normalizePath($fileFullPath), $fileName);
    }

    private function getResponse($fileFullPath, $fileName)
    {
        if (in_array('mod_xsendfile', apache_get_modules())) {
            $response = new Response('', 200, array(
                'X-Sendfile' => $fileFullPath,
                'Content-type' => 'application/octet-stream',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName)
            ));
        } else {
            $response = new BinaryFileResponse($fileFullPath);
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);
            //$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $fileName);
        }

        return $response;
    }
}