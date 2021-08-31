<?php

namespace Karkov\Kcms\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListPageController extends AbstractController
{
    /**
     * @Route("/admin/pages", name="kcms_admin_list_pages")
     */
    public function __invoke()
    {
        return $this->render('@Kcms/admin/page/index_page.html.twig', []);
    }
}
