<?php

namespace Karkov\Kcms\Service\JsTree;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Dto\JsTreeCopyDto;
use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Dto\JsTreeDeleteDto;
use Karkov\Kcms\Dto\JsTreeMoveNodeDto;
use Karkov\Kcms\Dto\JsTreeRenameNodeDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Exception\JsTreeManagerException;
use Karkov\Kcms\Service\Builder\NewContentBuilder;
use Karkov\Kcms\Service\Builder\NewHtmlPatternBuilder;
use Karkov\Kcms\Service\Builder\NewPageBuilder;
use Karkov\Kcms\Service\Builder\NewTreeBuilder;

class JsTreeManager
{
    const COPY_OF = 'Copy of ';

    private $entityManager;
    private $newContentBuilder;
    private $newTreeBuilder;
    private $newPageBuilder;
    private $newHtmlPatternBuilder;

    public function __construct(
        EntityManagerInterface $entityManager,
        NewContentBuilder $newContentBuilder,
        NewPageBuilder $newPageBuilder,
        NewTreeBuilder $newTreeBuilder,
        NewHtmlPatternBuilder $newHtmlPatternBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->newContentBuilder = $newContentBuilder;
        $this->newTreeBuilder = $newTreeBuilder;
        $this->newPageBuilder = $newPageBuilder;
        $this->newHtmlPatternBuilder = $newHtmlPatternBuilder;
    }

    public function createDirectory(JsTreeCreateNodeDto $dto): Tree
    {
        $dto = $this->sanityzeJsTreeCreateNodeDto($dto);
        $parentDirectory = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->parentId,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $parentDirectory) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->parentId));
        }

        $tree = $this->newTreeBuilder->build($dto, $parentDirectory);
        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        return $tree;
    }

    public function createNode(JsTreeCreateNodeDto $dto)
    {
        $dto = $this->sanityzeJsTreeCreateNodeDto($dto);
        $parentDirectory = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->parentId,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $parentDirectory) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->parentId));
        }

        switch ($dto->typeTree) {
            case 'content':
                $node = $this->newContentBuilder->build($dto, $parentDirectory);
                break;
            case 'page':
                $node = $this->newPageBuilder->build($dto, $parentDirectory);
                break;
            case 'patternhtml':
                $node = $this->newHtmlPatternBuilder->build($dto, $parentDirectory);
                break;
            default:
                throw new JsTreeManagerException(sprintf('%s is not a correct typeTree', $dto->typeTree));
        }

        $this->entityManager->persist($node);
        $this->entityManager->flush();

        return $node;
    }

    public function moveDirectory(JsTreeMoveNodeDto $dto): Tree
    {
        $dto = $this->sanityzeJsTreeMoveNodeDto($dto);
        $directory = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->nodeId,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $directory) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->nodeId));
        }

        if ($dto->oldParent !== $directory->getParent()->getId()) {
            throw new JsTreeManagerException(sprintf('Parent id is not equal to %s', $dto->oldParent));
        }

        if ($dto->newParent === $directory->getParent()->getId()) {
            throw new JsTreeManagerException(sprintf('Target parent id %s is already the current parent', $dto->newParent));
        }

        $parentDirectory = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->newParent,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $parentDirectory) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->newParent));
        }

        $directory->setParent($parentDirectory);
        $this->entityManager->flush();

        return $directory;
    }

    public function moveNode(JsTreeMoveNodeDto $dto)
    {
        $dto = $this->sanityzeJsTreeMoveNodeDto($dto);
        $className = $this->getClassName($dto);

        $node = $this->entityManager->getRepository($className)->findOneBy(
            [
                'id' => $dto->nodeId,
            ]
        );

        if (null === $node) {
            throw new JsTreeManagerException(sprintf('Node type %s with id %s not found', $dto->typeTree, $dto->nodeId));
        }

        if ($dto->oldParent !== $node->getParent()->getId()) {
            throw new JsTreeManagerException(sprintf('Parent id is not equal to %s', $dto->oldParent));
        }

        if ($dto->newParent === $node->getParent()->getId()) {
            throw new JsTreeManagerException(sprintf('Target parent id %s is already the current parent', $dto->newParent));
        }

        $newParent = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->newParent,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $newParent) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->newParent));
        }

        $node->setParent($newParent);
        $this->entityManager->flush();

        return $node;
    }

    public function renameDirectory(JsTreeRenameNodeDto $dto): Tree
    {
        $dto = $this->sanityzeJsTreeRenameNodeDto($dto);

        $directory = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->nodeId,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $directory) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->nodeId));
        }

        $directory->setName($dto->label);
        $this->entityManager->flush();

        return $directory;
    }

    public function renameNode(JsTreeRenameNodeDto $dto)
    {
        $dto = $this->sanityzeJsTreeRenameNodeDto($dto);
        $className = $this->getClassName($dto);

        $node = $this->entityManager->getRepository($className)->findOneBy(
            [
                'id' => $dto->nodeId,
            ]
        );

        if (null === $node) {
            throw new JsTreeManagerException(sprintf('Node with id %s not found', $dto->nodeId));
        }

        $node->setTitle($dto->label);
        $this->entityManager->flush();

        return $node;
    }

    public function deleteNode(JsTreeDeleteDto $dto): string
    {
        $dto = $this->sanityzeJsTreeDeleteNodeDto($dto);
        $className = $this->getClassName($dto);

        $node = $this->entityManager->getRepository($className)->findOneBy(
            [
                'id' => $dto->nodeId,
            ]
        );

        if (null === $node) {
            throw new JsTreeManagerException(sprintf('Node type %s with id %s not found', $dto->typeTree, $dto->nodeId));
        }

        $this->entityManager->remove($node);
        $this->entityManager->flush();

        return 'done';
    }

    public function deleteDirectory(JsTreeDeleteDto $dto): string
    {
        $dto = $this->sanityzeJsTreeDeleteNodeDto($dto);

        $directory = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->nodeId,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $directory) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->nodeId));
        }

        if ($this->isThereChildOnThisDirectory($directory)) {
            return 'refuse';
        }

        $this->entityManager->remove($directory);
        $this->entityManager->flush();

        return 'done';
    }

    public function copyNode(JsTreeCopyDto $dto): string
    {
        $dto = $this->sanityzeJsTreeCopyNodeDto($dto);
        $className = $this->getClassName($dto);

        /** @var Content $node */
        $node = $this->entityManager->getRepository($className)->findOneBy(
            [
                'id' => (int) $dto->nodeId,
            ]
        );

        if (null === $node) {
            throw new JsTreeManagerException(sprintf('Node type %s with id %s not found', $dto->typeTree, $dto->nodeId));
        }

        $parentDirectory = $this->entityManager->getRepository(Tree::class)->findOneBy(
            [
                'id' => $dto->parent,
                'type' => $dto->typeTree,
            ]
        );

        if (null === $parentDirectory) {
            throw new JsTreeManagerException(sprintf('Directory with id %s not found', $dto->parent));
        }

        switch ($dto->typeTree) {
            case 'content':
                $copiedNode = $this->copyContentNode($node, $parentDirectory);
                break;
            case 'page':
                $copiedNode = $this->copyPageNode($node, $parentDirectory);
                break;
            case 'patternhtml':
                $copiedNode = $this->copyHtmlPatternNode($node, $parentDirectory);
                break;
            default:
                throw new JsTreeManagerException(sprintf('Node type %s not allowed', $dto->typeTree));
        }

        $this->entityManager->persist($copiedNode);
        $this->entityManager->flush();

        return 'done';
    }

    private function copyContentNode($node, $parentDirectory): Content
    {
        /** @var Content $node */
        $copiedNode = clone $node;
        $copiedNode->setParent($parentDirectory);
        $copiedNode->setTitle(self::COPY_OF.$node->getTitle());

        foreach ($node->getPageContents() as $pageContent) {
            $copiedNode->removePageContent($pageContent);
        }

        foreach ($node->getContentLocals() as $contentLocal) {
            $copiedContentLocal = clone $contentLocal;
            $this->entityManager->persist($copiedContentLocal);
            $copiedNode->addContentLocal($copiedContentLocal);
        }

        return $copiedNode;
    }

    private function copyPageNode($node, $parentDirectory): Page
    {
        /** @var Page $node */
        $copiedNode = clone $node;
        $copiedNode->setParent($parentDirectory);
        $copiedNode->setTitle(self::COPY_OF.$node->getTitle());

        foreach ($node->getPageContents() as $pageContent) {
            $copiedPageContent = clone $pageContent;
            $this->entityManager->persist($copiedPageContent);
            $copiedNode->addPageContent($copiedPageContent);
        }

        return $copiedNode;
    }

    private function copyHtmlPatternNode($node, $parentDirectory): HtmlPattern
    {
        $copiedNode = clone $node;
        $copiedNode->setParent($parentDirectory);
        $copiedNode->setTitle(self::COPY_OF.$node->getTitle());

        return $copiedNode;
    }

    private function isThereChildOnThisDirectory(Tree $directory): bool
    {
        if (count($directory->getContents()) > 0 || count($directory->getChildren()) > 0 || count($directory->getHtmlPatterns()) > 0) {
            return true;
        }

        return false;
    }

    private function sanityzeJsTreeCreateNodeDto(JsTreeCreateNodeDto $dto): JsTreeCreateNodeDto
    {
        $dto->parentId = $this->sanityzeDirectoryId($dto->parentId);

        return $dto;
    }

    private function sanityzeJsTreeMoveNodeDto(JsTreeMoveNodeDto $dto): JsTreeMoveNodeDto
    {
        $dto->newParent = $this->sanityzeDirectoryId($dto->newParent);
        $dto->oldParent = $this->sanityzeDirectoryId($dto->oldParent);
        $dto->nodeId = $this->sanityzeDirectoryId($dto->nodeId);

        return $dto;
    }

    private function sanityzeJsTreeRenameNodeDto(JsTreeRenameNodeDto $dto): JsTreeRenameNodeDto
    {
        $dto->nodeId = $this->sanityzeDirectoryId($dto->nodeId);

        return $dto;
    }

    private function sanityzeJsTreeDeleteNodeDto(JsTreeDeleteDto $dto): JsTreeDeleteDto
    {
        $dto->nodeId = $this->sanityzeDirectoryId($dto->nodeId);

        return $dto;
    }

    private function sanityzeJsTreeCopyNodeDto(JsTreeCopyDto $dto): JsTreeCopyDto
    {
        $dto->parent = $this->sanityzeDirectoryId($dto->parent);

        return $dto;
    }

    private function sanityzeDirectoryId(string $id): int
    {
        return (int) str_replace('dir_', '', $id);
    }

    private function getClassName($dto): string
    {
        switch ($dto->typeTree) {
            case 'content':
                $className = Content::class;
                break;
            case 'page':
                $className = Page::class;
                break;
            case 'patternhtml':
                $className = HtmlPattern::class;
                break;
            default:
                throw new JsTreeManagerException(sprintf('%s is not a correct typeTree', $dto->typeTree));
        }

        return $className;
    }
}
