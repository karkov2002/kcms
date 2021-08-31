<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Form\EditContent\ContentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditContentController extends AbstractController
{
    private $config;
    private $entityManager;

    public function __construct(array $config, EntityManagerInterface $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/edit_content/{content}", name="admin_edit_content")
     */
    public function __invoke(Request $request, ?Content $content = null): Response
    {
        $formContent = $this->createForm(ContentType::class, $content, ['label' => false]);
        $formContent->handleRequest($request);

        if ($formContent->isSubmitted() && $formContent->isValid()) {
            $content = $formContent->getData();
            $this->entityManager->flush();
        }

        $formContentLocalized = [];
        $formContentLocalizedView = [];

        $formFactory = $this->get('form.factory');
        foreach ($this->config['multilingual']['local'] as $local) {
            $contentLocalized = $content->getContentLocalsByLocal($local)->first();

            if (empty($contentLocalized)) {
                $contentLocalized = $this->createContentLocalized($content, $local);
            }

            $formType = $content->getModule()::getFormType();

            $formContentLocalized[$local] = $formFactory->createNamed('content_local_'.$local, $formType, $contentLocalized);
            $formContentLocalized[$local]->handleRequest($request);

            if ($formContentLocalized[$local]->isSubmitted() && $formContentLocalized[$local]->isValid()) {
                $formContentLocalized[$local] = $formFactory->createNamed('content_local_'.$local, $formType, $formContentLocalized[$local]->getData());
                $this->entityManager->flush();
            }

            $formContentLocalizedView[$local] = $formContentLocalized[$local]->createView();
        }

        return $this->render('@Kcms/admin/content/edit_content.html.twig', [
            'formContent' => $formContent->createView(),
            'formContentLocalizedView' => $formContentLocalizedView,
        ]);
    }

    /**
     * @todo : create a builder service here
     */
    private function createContentLocalized(Content $content, string $local): ContentLocal
    {
        $contentLocalized = new ContentLocal();
        $contentLocalized
            ->setLocal($local)
            ->setContent($content)
        ;

        $this->entityManager->persist($contentLocalized);
        $this->entityManager->flush();

        return $contentLocalized;
    }
}
