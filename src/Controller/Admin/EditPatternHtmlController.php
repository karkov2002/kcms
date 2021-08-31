<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Form\EditPatternHtml\HtmlPatternType;
use Karkov\Kcms\Service\ContentLocal\HtmlPatternManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditPatternHtmlController extends AbstractController
{
    private $entityManager;
    private $htmlPatternManager;

    public function __construct(EntityManagerInterface $entityManager, HtmlPatternManager $htmlPatternManager)
    {
        $this->entityManager = $entityManager;
        $this->htmlPatternManager = $htmlPatternManager;
    }

    /**
     * @Route("/admin/edit_patternhtml/{htmlPattern}", name="admin_edit_pattern_html")
     */
    public function __invoke(Request $request, ?HtmlPattern $htmlPattern = null): Response
    {
        $form = $this->createForm(HtmlPatternType::class, $htmlPattern, ['label' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($htmlPattern->getContentLocals() as $contentLocal) {
                $this->htmlPatternManager->changeHtmlPattern($contentLocal, $htmlPattern);
            }

            $this->entityManager->flush();
        }

        return $this->render('@Kcms/admin/patternhtml/edit_patternhtml.html.twig', [
            'form' => $form->createView(),
            'htmlPattern' => $htmlPattern,
        ]);
    }
}
