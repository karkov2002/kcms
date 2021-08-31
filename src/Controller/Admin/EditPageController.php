<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Form\EditPage\AddSlugType;
use Karkov\Kcms\Form\EditPage\PageContentsType;
use Karkov\Kcms\Form\EditPage\PageType;
use Karkov\Kcms\Service\Provider\PageSlugProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditPageController extends AbstractController
{
    private $entityManager;
    private $pageSlugProvider;
    private $config;

    public function __construct(
        EntityManagerInterface $entityManager,
        PageSlugProvider $pageSlugProvider,
        array $config
    ) {
        $this->entityManager = $entityManager;
        $this->pageSlugProvider = $pageSlugProvider;
        $this->config = $config;
    }

    /**
     * @Route("/admin/edit_page/{page}")
     */
    public function __invoke(Request $request, Page $page): Response
    {
        // Edit page form
        $editPageForm = $this->createForm(PageType::class, $page, ['label' => false]);
        $editPageForm->handleRequest($request);
        if ($editPageForm->isSubmitted() && $editPageForm->isValid()) {
            $this->entityManager->flush();
        }

        // Add slug form
        $slugForm = $this->createForm(AddSlugType::class);
        $slugForm->handleRequest($request);
        if ($slugForm->isSubmitted() && $slugForm->isValid()) {
            $data = $slugForm->getData();
            $pageSlug = $this->pageSlugProvider->provideNewPageSlug($data['slug'], $data['local'], $page);
            $this->entityManager->persist($pageSlug);
            $this->entityManager->flush();
        }

        // Manage pageContent forms
        $pageContentForm = $this->createForm(PageContentsType::class, $page);
        $pageContentForm->handleRequest($request);
        if ($pageContentForm->isSubmitted() && $pageContentForm->isValid()) {
            $this->entityManager->flush();
            $pageContentForm = $this->createForm(PageContentsType::class, $page);
        }

        return $this->render('@Kcms/admin/page/edit_page.html.twig', [
            'page' => $page,
            'page_edit_form' => $editPageForm->createView(),
            'page_content_forms' => $pageContentForm->createView(),
            'add_slug_form' => $slugForm->createView(),
            'config' => $this->config,
        ]);
    }
}
