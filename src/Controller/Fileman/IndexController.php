<?php

namespace Karkov\Kcms\Controller\Fileman;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/fileman", name="fileman")
     */
    public function __invoke(): Response
    {
        return $this->render('@Kcms/Fileman/index.html.twig', []);
    }
}
