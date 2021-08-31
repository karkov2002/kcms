<?php

namespace Karkov\Kcms\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListPatternHtmlController extends AbstractController
{
    /**
     * @Route("/admin/patternhtmls", name="kcms_admin_list_pattern_html")
     */
    public function __invoke()
    {
        return $this->render('@Kcms/admin/patternhtml/index_patternhtml.html.twig', []);
    }
}
