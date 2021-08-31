<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Entity\Site;
use Karkov\Kcms\Form\EditSite\SiteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditSiteController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/edit_site/{site}", name="kcms_admin_edit_site")
     */
    public function __invoke(Request $request, Site $site): Response
    {
        $formEditSite = $this->createForm(SiteType::class, $site, ['label' => false]);
        $formEditSite->handleRequest($request);
        if ($formEditSite->isSubmitted() && $formEditSite->isValid()) {
            $site = $formEditSite->getData();
            $this->entityManager->persist($site);
            $this->entityManager->flush();
            $this->addFlash('success', 'Site is modified');
        }

        return $this->render('@Kcms/admin/site/edit_site.html.twig', [
            'formEditSite' => $formEditSite->createView(),
        ]);
    }
}
