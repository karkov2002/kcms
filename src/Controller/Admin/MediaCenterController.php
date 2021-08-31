<?php

namespace Karkov\Kcms\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaCenterController extends AbstractController
{
    /**
     * @Route("/admin/media_center", name="kcms_admin_fileman")
     */
    public function index(): Response
    {
        return $this->render('@Kcms/admin/media_center/default.html.twig', []);
    }

    /**
     * @Route("/admin/iframe/media_center", name="kcms_admin_fileman_iframe")
     */
    public function iframe(): Response
    {
        return $this->render('@Kcms/admin/media_center/iframe.html.twig', []);
    }
}
