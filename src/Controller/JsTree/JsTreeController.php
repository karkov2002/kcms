<?php

namespace Karkov\Kcms\Controller\JsTree;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Dto\GetJsTreeModuleDto;
use Karkov\Kcms\Dto\JsTreeCopyDto;
use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Dto\JsTreeDeleteDto;
use Karkov\Kcms\Dto\JsTreeMoveNodeDto;
use Karkov\Kcms\Dto\JsTreeRenameNodeDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Service\JsTree\JsTreeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class JsTreeController extends AbstractController
{
    public const EDIT_CONTENT_URL = '/kcms/admin/edit_content/';
    public const EDIT_PAGE_URL = '/kcms/admin/edit_page/';
    public const EDIT_HTML_PATTERN_URL = '/kcms/admin/edit_patternhtml/';
    public const DIRECTORY = 'directory';
    public const NODE = 'node';

    private $entityManager;
    private $serializer;
    private $jsTreeManager;

    public function __construct(
        JsTreeManager $jsTreeManager,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->jsTreeManager = $jsTreeManager;
    }

    /**
     * @Route("/admin/ajax/jstree/get_module", name="admin_ajax_jstree_get_module")
     */
    public function getModules(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $data = $this->serializer->deserialize($request->getContent(), GetJsTreeModuleDto::class, 'json');

        return new JsonResponse(['status' => 'done', 'html' => $this->renderView('@Kcms/admin/jsTree/jstree.html.twig', ['context' => $data])]);
    }

    /**
     * @Route("/admin/ajax/jstree/create_node", name="admin_ajax_jstree_create_node")
     */
    public function createNode(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $dto = $this->serializer->deserialize($request->getContent(), JsTreeCreateNodeDto::class, 'json');

        switch ($dto->type) {
            case self::DIRECTORY:
                $entity = $this->jsTreeManager->createDirectory($dto);
                break;
            case self::NODE:
                $entity = $this->jsTreeManager->createNode($dto);
                break;
            default:
                throw new HttpException(400, sprintf('Type %s not allowed', $dto->type));
        }

        return new JsonResponse(['status' => 'done', 'id' => $entity->getId()]);
    }

    /**
     * @Route("/admin/ajax/jstree/move_node", name="admin_ajax_jstree_move_node")
     */
    public function moveNode(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $dto = $this->serializer->deserialize($request->getContent(), JsTreeMoveNodeDto::class, 'json');

        switch ($dto->type) {
            case self::DIRECTORY:
                $this->jsTreeManager->moveDirectory($dto);
                break;
            case self::NODE:
                $this->jsTreeManager->moveNode($dto);
                break;
        }

        return new JsonResponse(['status' => 'done']);
    }

    /**
     * @Route("/admin/ajax/jstree/rename_node", name="admin_ajax_jstree_rename_node")
     */
    public function renameNode(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $dto = $this->serializer->deserialize($request->getContent(), JsTreeRenameNodeDto::class, 'json');

        switch ($dto->type) {
            case self::DIRECTORY:
                $this->jsTreeManager->renameDirectory($dto);
                break;
            case self::NODE:
                $this->jsTreeManager->renameNode($dto);
                break;
        }

        return new JsonResponse(['status' => 'done']);
    }

    /**
     * @Route("/admin/ajax/jstree/delete_node", name="admin_ajax_jstree_delete_node")
     */
    public function deleteNode(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $dto = $this->serializer->deserialize($request->getContent(), JsTreeDeleteDto::class, 'json');

        switch ($dto->type) {
            case self::DIRECTORY:
                $status = $this->jsTreeManager->deleteDirectory($dto);
                break;
            case self::NODE:
                $status = $this->jsTreeManager->deleteNode($dto);
                break;
            default:
                $status = 'refuse';
        }

        return new JsonResponse(['status' => $status]);
    }

    /**
     * @Route("/admin/ajax/jstree/copy_node", name="admin_ajax_jstree_copy_node")
     */
    public function copyNode(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'Not an ajax call');
        }

        $dto = $this->serializer->deserialize($request->getContent(), JsTreeCopyDto::class, 'json');
        $status = $this->jsTreeManager->copyNode($dto);

        return new JsonResponse(['status' => $status]);
    }

    /**
     * @Route("/admin/ajax/jstree/get_nodes/{type}", name="admin_ajax_jstree_get_node")
     */
    public function getNodes(Request $request, string $type): Response
    {
        $trees = $this->entityManager
            ->getRepository(Tree::class)
            ->findBy(['type' => $type])
        ;

        $arrayTree = [];

        /** @var Tree $tree */
        foreach ($trees as $tree) {
            $arrayTree[] = [
                'id' => 'dir_'.$tree->getId(),
                'parent' => $tree->getParent() ? 'dir_'.$tree->getParent()->getId() : '#',
                'li_attr' => ['type' => self::DIRECTORY],
                'text' => $tree->getName(),
            ];

            switch ($type) {
                case 'content':
                    /** @var Content $content */
                    foreach ($tree->getContents() as $content) {
                        $arrayTree[] = [
                            'id' => (string) $content->getId(),
                            'parent' => 'dir_'.$content->getParent()->getId(),
                            'text' => $content->getTitle(),
                            'type' => 'file',
                            'li_attr' => ['type' => self::NODE, 'href' => self::EDIT_CONTENT_URL.$content->getId()],
                            'a_attr' => ['href' => self::EDIT_CONTENT_URL.$content->getId()],
                        ];
                    }
                    break;

                case 'page':
                    /* @var Content $content */
                    foreach ($tree->getPages() as $page) {
                        $arrayTree[] = [
                            'id' => (string) $page->getId(),
                            'parent' => 'dir_'.$page->getParent()->getId(),
                            'text' => $page->getTitle(),
                            'type' => 'file',
                            'li_attr' => ['type' => self::NODE, 'href' => self::EDIT_PAGE_URL.$page->getId()],
                            'a_attr' => ['href' => self::EDIT_PAGE_URL.$page->getId()],
                        ];
                    }
                    break;

                case 'patternhtml':
                    /** @var HtmlPattern $htmlPattern */
                    foreach ($tree->getHtmlPatterns() as $htmlPattern) {
                        $arrayTree[] = [
                            'id' => (string) $htmlPattern->getId(),
                            'parent' => 'dir_'.$htmlPattern->getParent()->getId(),
                            'text' => $htmlPattern->getTitle(),
                            'type' => 'file',
                            'li_attr' => ['type' => self::NODE, 'href' => self::EDIT_HTML_PATTERN_URL.$htmlPattern->getId()],
                            'a_attr' => ['href' => self::EDIT_HTML_PATTERN_URL.$htmlPattern->getId()],
                        ];
                    }
                    break;
            }
        }

        return new JsonResponse($arrayTree);
    }
}
