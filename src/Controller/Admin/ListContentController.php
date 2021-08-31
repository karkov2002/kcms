<?php

namespace Karkov\Kcms\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListContentController extends AbstractController
{
    /**
     * @Route("/admin/contents", name="kcms_admin_list_content")
     */
    public function __invoke()
    {
        return $this->render('@Kcms/admin/content/index_content.html.twig', []);
    }
}
