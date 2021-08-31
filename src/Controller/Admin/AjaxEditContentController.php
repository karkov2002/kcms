<?php

namespace Karkov\Kcms\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Dto\DetachHtmlPatternDto;
use Karkov\Kcms\Dto\SelectHtmlPatternDto;
use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Service\ContentLocal\HtmlPatternManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AjaxEditContentController extends AbstractController
{
    private $entityManager;
    private $serializer;
    private $htmlPatternManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        HtmlPatternManager $htmlPatternManager
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->htmlPatternManager = $htmlPatternManager;
    }

    /**
     * @Route("/admin/ajax/edit_content/select_htmlpattern", name="admin_ajax_editcontent_select_htmlpattern")
     */
    public function selectHtmlPattern(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), SelectHtmlPatternDto::class, 'json');

        $htmlPattern = $this->entityManager->getRepository(HtmlPattern::class)->find($data->htmlPatternId);

        if (null === $htmlPattern) {
            throw new HttpException(400, sprintf('htmlPattern %s not found', $data->htmlPatternId));
        }

        $contentLocal = $this->entityManager->getRepository(ContentLocal::class)->find($data->contentLocalId);

        if (null === $contentLocal) {
            throw new HttpException(400, sprintf('contentLocal %s not found', $data->contentLocalId));
        }

        $this->htmlPatternManager->changeHtmlPattern($contentLocal, $htmlPattern);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'done']);
    }

    /**
     * @Route("/admin/ajax/edit_content/detach_htmlpattern", name="admin_ajax_editcontent_detach_htmlpattern")
     */
    public function detachHtmlPattern(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), DetachHtmlPatternDto::class, 'json');

        $contentLocal = $this->entityManager->getRepository(ContentLocal::class)->find($data->contentLocalId);

        if (null === $contentLocal) {
            throw new HttpException(400, sprintf('contentLocal %s not found', $data->contentLocalId));
        }

        $contentLocal->setHtmlPattern(null);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'done']);
    }
}
