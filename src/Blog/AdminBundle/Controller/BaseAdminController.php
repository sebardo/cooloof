<?php

namespace Blog\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseAdminController extends Controller
{
    public function getTemplate()
    {
        /** @var $templateService \Blog\CoreBundle\Service\TemplateService */
        $templateService = $this->get('template.service');
        return $templateService->getTemplateAdmin();
    }
}
