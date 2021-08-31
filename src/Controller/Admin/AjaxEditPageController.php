<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Dto\AddPageContentDto;
use Karkov\Kcms\Dto\ChangeRankDto;
use Karkov\Kcms\Dto\ChangeZoneDto;
use Karkov\Kcms\Dto\DeletePageContentDto;
use Karkov\Kcms\Dto\DeletePageSlugDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageContent;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Service\Helper\DateTimer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AjaxEditPageController extends AbstractController
{
    private $entityManager;
    private $serializer;
    private $dateTimer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        DateTimer $dateTimer
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->dateTimer = $dateTimer;
    }

    /**
     * @Route("/admin/ajax/edit_page/delete_slug", name="admin_ajax_editpage_delete_slug")
     */
    public function deleteSlug(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), DeletePageSlugDto::class, 'json');

        $pageSlug = $this->entityManager->getRepository(PageSlug::class)->find($data->pageSlugId);

        if (null === $pageSlug) {
            throw new HttpException(400, sprintf('pageSlug %s not found', $data->pageSlugId));
        }

        $this->entityManager->remove($pageSlug);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'done']);
    }

    /**
     * @Route("/admin/ajax/edit_page/change_zone", name="admin_ajax_editpage_zone")
     */
    public function changeZone(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), ChangeZoneDto::class, 'json');

        $pageContent = $this->entityManager->getRepository(PageContent::class)->find($data->pageContentId);

        if (null === $pageContent) {
            throw new HttpException(400, sprintf('pageContent %s not found', $data->pageContentId));
        }

        if ($pageContent->getPage()->getId() !== $data->pageId) {
            throw new HttpException(400, sprintf('page %s not found', $data->pageId));
        }

        $pageContent->setZone($data->zone);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'done']);
    }

    /**
     * @Route("/admin/ajax/edit_page/change_rank", name="admin_ajax_editpage_rank")
     */
    public function changeRank(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), ChangeRankDto::class, 'json');

        $page = $this->entityManager->getRepository(Page::class)->find($data->pageId);

        if (null === $page) {
            throw new HttpException(400, sprintf('page %s not found', $data->pageId));
        }

        $pageContentsByZone = $page->getPageContentsByZone($data->zone);

        /** @var PageContent $pageContent */
        foreach ($pageContentsByZone as $pageContent) {
            if (!in_array($pageContent->getId(), $data->rank)) {
                throw new HttpException(400, sprintf('pageContent %s is missing in rank values', $pageContent->getId()));
            }

            $rank = array_search($pageContent->getId(), $data->rank) + 1;
            $pageContent->setRank($rank);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'done']);
    }

    /**
     * @Route("/admin/ajax/edit_page/delete_pagecontent", name="admin_ajax_editpage_delete_pagecontent")
     */
    public function deletePageContent(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), DeletePageContentDto::class, 'json');

        $pageContent = $this->entityManager->getRepository(PageContent::class)->find($data->pageContentId);

        if (null === $pageContent) {
            throw new HttpException(400, sprintf('pageContent %s not found', $data->pageContentId));
        }

        if ($pageContent->getPage()->getId() !== $data->pageId) {
            throw new HttpException(400, sprintf('page %s not found', $data->pageId));
        }

        $this->entityManager->remove($pageContent);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'done']);
    }

    /**
     * @Route("/admin/ajax/edit_page/add_pagecontent", name="admin_ajax_editpage_add_pagecontent")
     */
    public function addPageContent(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), AddPageContentDto::class, 'json');

        $page = $this->entityManager->getRepository(Page::class)->find($data->pageId);

        if (null === $page) {
            throw new HttpException(400, sprintf('page %s not found', $data->pageId));
        }

        $content = $this->entityManager->getRepository(Content::class)->find($data->contentId);

        if (null === $content) {
            throw new HttpException(400, sprintf('content %s not found', $data->contentId));
        }

        $pageContent = new PageContent();
        $pageContent
            ->setZone($data->zone)
            ->setRank(1)
            ->setPage($page)
            ->setContent($content)
            ->setDateStart($this->dateTimer->get('2020-01-01'))
            ->setDateEnd($this->dateTimer->get('2020-01-01'))
        ;

        $this->entityManager->persist($pageContent);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'done']);
    }
}
