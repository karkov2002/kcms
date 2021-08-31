<?php

namespace Karkov\Kcms\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="kcms_admin_hp")
     */
    public function __invoke(): Response
    {
        return $this->render('@Kcms/admin/admin.html.twig', []);
    }
}
