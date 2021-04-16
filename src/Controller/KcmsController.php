<?php

namespace Karkov\Kcms\Controller;

use Karkov\Kcms\KcmsBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KcmsController extends AbstractController
{
    private $config;

    public function __construct(KcmsBundle $KcmsBundle)
    {
        $this->config = $KcmsBundle->getConfig();
    }

    /**
     * @Route("/kcms/{slug}", name="kcms_controller")
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $local = $request->getLocale();

        dump([$local, $slug, $this->config]);

        return $this->render('@Kcms/default/default.html.twig', []);
    }
}
