<?php

namespace Blog\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseFrontController extends Controller
{
    public function getTemplate()
    {
        /** @var $templateService \Blog\CoreBundle\Service\TemplateService */
        $templateService = $this->get('template.service');
        return $templateService->getTemplate();
    }
}
