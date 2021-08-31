<?php

namespace Karkov\Kcms\Controller;

use Karkov\Kcms\Dto\KcmsDto;
use Karkov\Kcms\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class KcmsController extends AbstractController
{
    public function __invoke(KcmsDto $kcms, Request $request): Response
    {
        if (null === $kcms->getPage()) {
            throw new NotFoundHttpException(sprintf('Kcms page not found for request %s', $kcms->getRequestDto()));
        }

        return new Response($this->renderView($kcms->getPage()->getTemplate(), ['kcms' => $kcms]));
    }
}
