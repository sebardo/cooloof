<?php

namespace Blog\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DashboardController extends BaseAdminController
{
    /**
     * @Route("/", name="admin_dashboard")
     */

    public function dashboardAction()
    {
        return $this->redirect($this->generateUrl('admin_post_list'));
        // return $this->render("AdminBundle:Dashboard:{$this->getTemplate()}/dashboard.html.twig");
    }
}
