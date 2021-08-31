<?php

namespace Karkov\Kcms\Tests\Service\JsTree;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Dto\JsTreeCopyDto;
use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Dto\JsTreeDeleteDto;
use Karkov\Kcms\Dto\JsTreeMoveNodeDto;
use Karkov\Kcms\Dto\JsTreeRenameNodeDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageContent;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Exception\JsTreeManagerException;
use Karkov\Kcms\Repository\ContentRepository;
use Karkov\Kcms\Repository\PageRepository;
use Karkov\Kcms\Repository\TreeRepository;
use Karkov\Kcms\Service\Builder\NewContentBuilder;
use Karkov\Kcms\Service\Builder\NewHtmlPatternBuilder;
use Karkov\Kcms\Service\Builder\NewPageBuilder;
use Karkov\Kcms\Service\Builder\NewTreeBuilder;
use Karkov\Kcms\Service\JsTree\JsTreeManager;
use PHPUnit\Framework\TestCase;

class JsTreeManagerTest extends TestCase
{
    private $call;
    private $callOnCopy;

    public function setUp(): void
    {
        $this->call = 0;
        $this->callOnCopy = 0;
    }

    public function testCreateNewDirectory()
    {
        // Given
        $rootTree = new Tree();
        $rootTree
            ->setName('root')
            ->setType('page')
        ;

        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn($rootTree);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);

        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeCreateNodeDto();
        $dto->parentId = 0;
        $dto->typeTree = 'page';
        $dto->label = 'a new page directory';
        $tree = $jsTreeManager->createDirectory($dto);

        //Then
        $expected = new Tree();
        $expected
            ->setType('page')
            ->setName('a new page directory')
            ->setParent($rootTree)
        ;

        $this->assertEquals($expected, $tree);
    }

    public function testCreateNewDirectoryWithANonExistingParent()
    {
        // Given
        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn(null);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);

        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 0 not found');

        // When
        $dto = new JsTreeCreateNodeDto();
        $dto->parentId = 0;
        $dto->typeTree = 'page';
        $dto->label = 'a new page';
        $jsTreeManager->createDirectory($dto);
    }

    public function testCreateNodeWithANonExistingParent()
    {
        // Given
        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn(null);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);

        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        //Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 0 not found');

        // When
        $dto = new JsTreeCreateNodeDto();
        $dto->parentId = 0;
        $dto->typeTree = 'page';
        $dto->label = 'a new page';
        $jsTreeManager->createNode($dto);
    }

    public function testCreateNodeWithAWrongType()
    {
        // Given
        $rootTree = new Tree();
        $rootTree
            ->setName('root')
            ->setType('page')
        ;

        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn($rootTree);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);

        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        //Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('a_wrong_type is not a correct typeTree');

        // When
        $dto = new JsTreeCreateNodeDto();
        $dto->parentId = 0;
        $dto->typeTree = 'a_wrong_type';
        $dto->label = 'a new content';
        $jsTreeManager->createNode($dto);
    }

    public function testCreateNewPageNode()
    {
        // Given
        $rootTree = new Tree();
        $rootTree
            ->setName('root')
            ->setType('page')
        ;

        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn($rootTree);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);

        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeCreateNodeDto();
        $dto->parentId = 0;
        $dto->typeTree = 'page';
        $dto->label = 'a new page';
        $node = $jsTreeManager->createNode($dto);

        //Then
        $expected = new Page();
        $expected
            ->setParent($rootTree)
            ->setTemplate('@KcmsBundle/default/kcms.default.html.twig')
            ->setTitle('a new page')
        ;

        $this->assertEquals($expected, $node);
    }

    public function testCreateNewContentNode()
    {
        // Given
        $rootTree = new Tree();
        $rootTree
            ->setName('root')
            ->setType('content')
        ;

        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn($rootTree);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);

        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeCreateNodeDto();
        $dto->parentId = 0;
        $dto->typeTree = 'content';
        $dto->label = 'a new content';
        $node = $jsTreeManager->createNode($dto);

        //Then
        $expected = new Content();
        $expected
            ->setParent($rootTree)
            ->setModule('Karkov\Kcms\Modules\TextModule')
            ->setTitle('a new content')
        ;

        $this->assertEquals($expected, $node);
    }

    public function testMoveNode()
    {
        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'node';
        $dto->nodeId = 0;
        $dto->oldParent = 1;
        $dto->newParent = 2;

        $node = $jsTreeManager->moveNode($dto);

        //Then
        $expectedParentTree = new Tree();
        $expectedParentTree
            ->setType('page')
            ->setName('new directory')
            ->setId(2)
        ;

        $expected = new Page();
        $expected
            ->setTemplate('template.html.twig')
            ->setTitle('a page')
            ->setParent($expectedParentTree)
        ;

        $this->assertEquals($expected, $node);
    }

    public function getRepositoryOnMoveNode()
    {
        $args = func_get_args();

        if (Page::class === $args[0]) {
            $currentTree = new Tree();
            $currentTree
                ->setId(1)
                ->setName('current directory')
                ->setType('page')
            ;
            $page = new Page();
            $page
                ->setTitle('a page')
                ->setTemplate('template.html.twig')
                ->setParent($currentTree);

            $pageRepositoryMock = $this->createMock(PageRepository::class);
            $pageRepositoryMock->method('findOneBy')->willReturn($page);

            return $pageRepositoryMock;
        }

        if (Tree::class === $args[0]) {
            $newTree = new Tree();
            $newTree
                ->setId(2)
                ->setName('new directory')
                ->setType('page')
            ;

            $treeRepositoryMock = $this->createMock(TreeRepository::class);

            if (10 == $this->call) {
                $treeRepositoryMock->method('findOneBy')->willReturn(null);
            } else {
                $treeRepositoryMock->method('findOneBy')->willReturn($newTree);
            }

            return $treeRepositoryMock;
        }

        return null;
    }

    public function testGetExceptionWhenMoveNodeWithAWrongParent()
    {
        // Given
        $pageRepository = $this->createMock(PageRepository::class);
        $pageRepository->method('findOneBy')->willReturn(null);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($pageRepository);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Node type page with id 0 not found');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'node';
        $dto->nodeId = 0;
        $dto->oldParent = 1;
        $dto->newParent = 2;

        $jsTreeManager->moveNode($dto);
    }

    public function testGetExceptionWhenMoveANonExistingNode()
    {
        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Parent id is not equal to 10');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'node';
        $dto->nodeId = 0;
        $dto->oldParent = 10;
        $dto->newParent = 2;

        $jsTreeManager->moveNode($dto);
    }

    public function testGetExceptionWhenMoveNodeAndTargetIsTheCurrentDirectory()
    {
        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Target parent id 1 is already the current parent');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'node';
        $dto->nodeId = 0;
        $dto->oldParent = 1;
        $dto->newParent = 1;

        $jsTreeManager->moveNode($dto);
    }

    public function testGetExceptionWhenMoveNodeAndTargetIsANonExistingDirectory()
    {
        $this->call = 10;

        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 2 not found');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'node';
        $dto->nodeId = 0;
        $dto->oldParent = 1;
        $dto->newParent = 2;

        $jsTreeManager->moveNode($dto);
    }

    public function testMoveDirectory()
    {
        // Given
        $this->call = 0;
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveDirectory']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'directory';
        $dto->nodeId = 2;
        $dto->oldParent = 1;
        $dto->newParent = 3;

        $directory = $jsTreeManager->moveDirectory($dto);

        //Then
        $rootTree = new Tree();
        $rootTree->setId(1)
            ->setName('root directory')
            ->setType('page')
        ;

        $newParentDirectory = new Tree();
        $newParentDirectory
            ->setId(3)
            ->setName('new parent directory')
            ->setType('page')
            ->setParent($rootTree)
        ;

        $expected = new Tree();
        $expected
            ->setType('page')
            ->setName('current directory')
            ->setId(2)
            ->setParent($newParentDirectory)
        ;

        $this->assertEquals($expected, $directory);
    }

    public function getRepositoryOnMoveDirectory()
    {
        $rootTree = new Tree();
        $rootTree->setId(1)
            ->setName('root directory')
            ->setType('page')
        ;

        $currentTree = new Tree();
        $currentTree
            ->setId(2)
            ->setName('current directory')
            ->setType('page')
            ->setParent($rootTree)
        ;

        $newParentDirectory = new Tree();
        $newParentDirectory
            ->setId(3)
            ->setName('new parent directory')
            ->setType('page')
            ->setParent($rootTree)
        ;

        $treeRepositoryMock = $this->createMock(TreeRepository::class);

        ++$this->call;

        // For normal cases
        if (1 === $this->call) {
            $treeRepositoryMock->method('findOneBy')->willReturn($currentTree);
        }
        if (2 === $this->call) {
            $treeRepositoryMock->method('findOneBy')->willReturn($newParentDirectory);
        }

        // For testing when target directory does not exist
        if (11 === $this->call) {
            $treeRepositoryMock->method('findOneBy')->willReturn($currentTree);
        }
        if (12 === $this->call) {
            $treeRepositoryMock->method('findOneBy')->willReturn(null);
        }

        return $treeRepositoryMock;
    }

    public function testGetExceptionWhenMoveDirectoryAndProvidedParentIsNotTheCurrentParent()
    {
        // Given
        $this->call = 0;
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveDirectory']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Parent id is not equal to 4');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'directory';
        $dto->nodeId = 2;
        $dto->oldParent = 4;
        $dto->newParent = 3;

        $jsTreeManager->moveDirectory($dto);
    }

    public function testGetExceptionWhenMoveDirectoryAndTargetIsAlreadyTheParentDirectory()
    {
        // Given
        $this->call = 0;
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveDirectory']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Target parent id 1 is already the current parent');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'directory';
        $dto->nodeId = 2;
        $dto->oldParent = 1;
        $dto->newParent = 1;

        $jsTreeManager->moveDirectory($dto);
    }

    public function testGetExceptionWhenMoveDirectoryAndDirectoryNotExist()
    {
        // Given
        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn(null);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 2 not found');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'directory';
        $dto->nodeId = 2;
        $dto->oldParent = 1;
        $dto->newParent = 3;

        $jsTreeManager->moveDirectory($dto);
    }

    public function testGetExceptionWhenMoveDirectoryAndNewDirectoryNotExist()
    {
        // Given
        $this->call = 10;
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnMoveDirectory']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 3 not found');

        // When
        $dto = new JsTreeMoveNodeDto();
        $dto->typeTree = 'page';
        $dto->type = 'directory';
        $dto->nodeId = 2;
        $dto->oldParent = 1;
        $dto->newParent = 3;

        $jsTreeManager->moveDirectory($dto);
    }

    public function testRenameDirectory()
    {
        // Given
        $rootTree = new Tree();
        $rootTree
            ->setId(1)
            ->setName('root')
            ->setType('page')
        ;
        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn($rootTree);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeRenameNodeDto();
        $dto->nodeId = 1;
        $dto->typeTree = 'page';
        $dto->type = 'directory';
        $dto->label = 'new name of root directory';

        $tree = $jsTreeManager->renameDirectory($dto);

        // Then
        $expected = new Tree();
        $expected
            ->setId(1)
            ->setName('new name of root directory')
            ->setType('page')
        ;
        $this->assertEquals($expected, $tree);
    }

    public function testExceptionWhenRenameANonExistingDirectory()
    {
        // Given
        $treeRepositoryMock = $this->createMock(TreeRepository::class);
        $treeRepositoryMock->method('findOneBy')->willReturn(null);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($treeRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 1 not found');

        // When
        $dto = new JsTreeRenameNodeDto();
        $dto->nodeId = 1;
        $dto->typeTree = 'page';
        $dto->type = 'directory';
        $dto->label = 'new name of root directory';

        $jsTreeManager->renameDirectory($dto);
    }

    public function testRenameNode()
    {
        // Given
        $content = new Content();
        $content->setTitle('a content');

        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $contentRepositoryMock->method('findOneBy')->willReturn($content);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeRenameNodeDto();
        $dto->nodeId = 1;
        $dto->typeTree = 'content';
        $dto->type = 'node';
        $dto->label = 'new content name';

        $tree = $jsTreeManager->renameNode($dto);

        // Then
        $expected = new Content();
        $expected->setTitle('new content name');
        $this->assertEquals($expected, $tree);
    }

    public function testExceptionWhenRenameANonExistingNode()
    {
        // Given
        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $contentRepositoryMock->method('findOneBy')->willReturn(null);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Node with id 1 not found');

        // When
        $dto = new JsTreeRenameNodeDto();
        $dto->nodeId = 1;
        $dto->typeTree = 'content';
        $dto->type = 'node';
        $dto->label = 'new content name';

        $jsTreeManager->renameNode($dto);
    }

    public function testDeleteNode()
    {
        // Given
        $content = new Content();
        $content->setTitle('a content');

        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $contentRepositoryMock->method('findOneBy')->willReturn($content);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeDeleteDto();
        $dto->typeTree = 'content';
        $dto->type = 'node';
        $dto->nodeId = 1;

        $result = $jsTreeManager->deleteNode($dto);

        // Then
        $this->assertEquals('done', $result);
    }

    public function testExceptionWhenDeleteANonExistingNode()
    {
        // Given
        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $contentRepositoryMock->method('findOneBy')->willReturn(null);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Node type content with id 1 not found');

        // When
        $dto = new JsTreeDeleteDto();
        $dto->typeTree = 'content';
        $dto->type = 'node';
        $dto->nodeId = 1;

        $jsTreeManager->deleteNode($dto);
    }

    public function testRefuseWhenDeleteDirectory()
    {
        // Given
        $currentTree = new Tree();
        $currentTree
            ->setId(2)
            ->setName('current directory')
            ->setType('page')
        ;

        $rootTree = new Tree();
        $rootTree->setId(1)
            ->setName('root directory')
            ->setType('page')
            ->addChild($currentTree)
        ;

        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $contentRepositoryMock->method('findOneBy')->willReturn($rootTree);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeDeleteDto();
        $dto->typeTree = 'content';
        $dto->type = 'directory';
        $dto->nodeId = 1;

        $result = $jsTreeManager->deleteDirectory($dto);

        // Then
        $this->assertEquals('refuse', $result);
    }

    public function testDeleteDirectory()
    {
        // Given
        $emptyTree = new Tree();
        $emptyTree->setId(1)
            ->setName('an empty directory')
            ->setType('page')
        ;

        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $contentRepositoryMock->method('findOneBy')->willReturn($emptyTree);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $dto = new JsTreeDeleteDto();
        $dto->typeTree = 'content';
        $dto->type = 'directory';
        $dto->nodeId = 1;

        $result = $jsTreeManager->deleteDirectory($dto);

        // Then
        $this->assertEquals('done', $result);
    }

    public function testExceptionWhenDeleteANonExstingDirectory()
    {
        // Given
        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $contentRepositoryMock->method('findOneBy')->willReturn(null);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 1 not found');

        // When
        $dto = new JsTreeDeleteDto();
        $dto->typeTree = 'content';
        $dto->type = 'directory';
        $dto->nodeId = 1;

        $jsTreeManager->deleteDirectory($dto);
    }

    public function testExceptionWhenGetClassNameWithAWrongTypeTree()
    {
        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('not_existing_type_tree is not a correct typeTree');

        // When
        $dto = new JsTreeDeleteDto();
        $dto->typeTree = 'not_existing_type_tree'; // <== Must be 'page' or 'content"
        $dto->type = 'node';
        $dto->nodeId = 1;

        $jsTreeManager->deleteNode($dto);
    }

    public function testCopyNodeContent()
    {
        $this->callOnCopy = 0;

        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnCopyNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $jsTreeCopyDto = new JsTreeCopyDto();
        $jsTreeCopyDto->typeTree = 'content';
        $jsTreeCopyDto->type = 'node';
        $jsTreeCopyDto->parent = 1;
        $jsTreeCopyDto->nodeId = 1;

        $result = $jsTreeManager->copyNode($jsTreeCopyDto);

        // Then
        $this->assertEquals('done', $result);
    }

    public function testCopyNodePage()
    {
        $this->callOnCopy = 10;

        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnCopyNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // When
        $jsTreeCopyDto = new JsTreeCopyDto();
        $jsTreeCopyDto->typeTree = 'page';
        $jsTreeCopyDto->type = 'node';
        $jsTreeCopyDto->parent = 1;
        $jsTreeCopyDto->nodeId = 1;

        $result = $jsTreeManager->copyNode($jsTreeCopyDto);

        // Then
        $this->assertEquals('done', $result);
    }

    public function testExceptionTypeTreeNotExistWhenCopyNodePage()
    {
        $this->callOnCopy = 0;

        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnCopyNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('christmas is not a correct typeTree');

        // When
        $jsTreeCopyDto = new JsTreeCopyDto();
        $jsTreeCopyDto->typeTree = 'christmas';
        $jsTreeCopyDto->type = 'node';
        $jsTreeCopyDto->parent = 1;
        $jsTreeCopyDto->nodeId = 1;

        $jsTreeManager->copyNode($jsTreeCopyDto);
    }

    public function testExceptionNodeNotFoundDuringCopyNode()
    {
        // Given
        $contentRepositoryMock = $this->createMock(ContentRepository::class);
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturn($contentRepositoryMock);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Node type content with id 1 not found');

        // When
        $jsTreeCopyDto = new JsTreeCopyDto();
        $jsTreeCopyDto->typeTree = 'content';
        $jsTreeCopyDto->type = 'node';
        $jsTreeCopyDto->parent = 1;
        $jsTreeCopyDto->nodeId = 1;

        $jsTreeManager->copyNode($jsTreeCopyDto);
    }

    public function testExceptionDirectoryNotFoundDuringCopyNode()
    {
        $this->callOnCopy = 20;

        // Given
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->method('getRepository')->willReturnCallback([$this, 'getRepositoryOnCopyNode']);
        $newContentBuilder = new NewContentBuilder();
        $newPageBuilder = new NewPageBuilder();
        $newTreeBuilder = new NewTreeBuilder();
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeManager = new JsTreeManager($entityManagerMock, $newContentBuilder, $newPageBuilder, $newTreeBuilder, $newHtmlPatternBuilder);

        // Then
        $this->expectException(JsTreeManagerException::class);
        $this->expectExceptionMessage('Directory with id 1 not found');

        // When
        $jsTreeCopyDto = new JsTreeCopyDto();
        $jsTreeCopyDto->typeTree = 'content';
        $jsTreeCopyDto->type = 'node';
        $jsTreeCopyDto->parent = 1;
        $jsTreeCopyDto->nodeId = 1;

        $jsTreeManager->copyNode($jsTreeCopyDto);
    }

    public function getRepositoryOnCopyNode()
    {
        ++$this->callOnCopy;

        $rootTree = new Tree();
        $rootTree->setId(1)
            ->setName('root directory')
            ->setType('page');

        if (1 === $this->callOnCopy) {
            $contentLocal = new ContentLocal();
            $contentLocal
                ->setLocal('fr_FR')
                ->setRawContent('Hello')
            ;

            $pageContent = new PageContent();

            $content = new Content();
            $content
                ->setTitle('a content')
                ->setModule('Karkov\Kcms\Modules\TextModule')
                ->setParent($rootTree)
                ->addContentLocal($contentLocal)
                ->addPageContent($pageContent)
            ;

            $contentRepositoryMock = $this->createMock(ContentRepository::class);
            $contentRepositoryMock->method('findOneBy')->willReturn($content);

            return $contentRepositoryMock;
        }

        if (2 === $this->callOnCopy) {
            $treeRepositoryMock = $this->createMock(TreeRepository::class);
            $treeRepositoryMock->method('findOneBy')->willReturn($rootTree);

            return $treeRepositoryMock;
        }

        if (11 === $this->callOnCopy) {
            $pageContent = new PageContent();
            $page = new Page();
            $page
                ->setTitle('a page')
                ->addPageContent($pageContent)
            ;

            $pageRepositoryMock = $this->createMock(PageRepository::class);
            $pageRepositoryMock->method('findOneBy')->willReturn($page);

            return $pageRepositoryMock;
        }

        if (12 === $this->callOnCopy) {
            $treeRepositoryMock = $this->createMock(TreeRepository::class);
            $treeRepositoryMock->method('findOneBy')->willReturn($rootTree);

            return $treeRepositoryMock;
        }

        if (21 === $this->callOnCopy) {
            $contentLocal = new ContentLocal();
            $contentLocal
                ->setLocal('fr_FR')
                ->setRawContent('Hello')
            ;

            $pageContent = new PageContent();

            $content = new Content();
            $content
                ->setTitle('a content')
                ->setModule('Karkov\Kcms\Modules\TextModule')
                ->setParent($rootTree)
                ->addContentLocal($contentLocal)
                ->addPageContent($pageContent)
            ;

            $contentRepositoryMock = $this->createMock(ContentRepository::class);
            $contentRepositoryMock->method('findOneBy')->willReturn($content);

            return $contentRepositoryMock;
        }

        if (22 === $this->callOnCopy) {
            $treeRepositoryMock = $this->createMock(TreeRepository::class);
            $treeRepositoryMock->method('findOneBy')->willReturn(null);

            return $treeRepositoryMock;
        }
    }
}
