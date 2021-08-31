<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Entity\Site;
use Karkov\Kcms\Form\EditSite\SiteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListSiteController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/sites", name="kcms_admin_list_site")
     */
    public function __invoke(Request $request): Response
    {
        $formCreateNewSite = $this->createForm(SiteType::class, null, ['label' => false]);
        $formCreateNewSite->handleRequest($request);
        if ($formCreateNewSite->isSubmitted() && $formCreateNewSite->isValid()) {
            $site = $formCreateNewSite->getData();
            if (null !== $this->entityManager->getRepository(Site::class)->findOneBy(['domain' => $site->getDomain()])) {
                $this->addFlash('error', sprintf('A site with the domain "%s" already exist', $site->getDomain()));
            } else {
                $this->entityManager->persist($site);
                $this->entityManager->flush();
                $this->addFlash('success', 'New site is created');
            }

            $formCreateNewSite = $this->createForm(SiteType::class);
        }

        $sites = $this->entityManager->getRepository(Site::class)->findAll();

        return $this->render('@Kcms/admin/site/index_site.html.twig', [
            'sites' => $sites,
            'formCreateNewSite' => $formCreateNewSite->createView(),
        ]);
    }
}
