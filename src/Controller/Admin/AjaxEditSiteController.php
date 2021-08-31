<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Dto\DeleteSiteDto;
use Karkov\Kcms\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AjaxEditSiteController extends AbstractController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/admin/ajax/edit_site/delete_site", name="kcms_admin_ajax_delete_site")
     */
    public function deleteSite(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), DeleteSiteDto::class, 'json');
        $site = $this->entityManager->getRepository(Site::class)->find($data->siteId);

        if (null === $site) {
            throw new HttpException(400, sprintf('site %s not found', $data->siteId));
        }

        if (count($site->getPages()) > 0) {
            return new JsonResponse(['status' => 'ko']);
        }

        $this->entityManager->remove($site);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}
