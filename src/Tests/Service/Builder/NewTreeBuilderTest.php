<?php

namespace Karkov\Kcms\Tests\Service\Builder;

use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Service\Builder\NewTreeBuilder;
use PHPUnit\Framework\TestCase;

class NewTreeBuilderTest extends TestCase
{
    public function testBuild()
    {
        // Given
        $treeBuilder = new NewTreeBuilder();
        $parentTree = new Tree();
        $parentTree
            ->setType('content')
            ->setName('root')
        ;
        $dto = new JsTreeCreateNodeDto();
        $dto->label = 'a new directory';
        $dto->typeTree = 'content';
        $dto->type = 'directory';

        // When
        $tree = $treeBuilder->build($dto, $parentTree);

        // Then
        $expectedTree = new Tree();
        $expectedTree
            ->setName('a new directory')
            ->setType('content')
            ->setParent($parentTree)
        ;

        $this->assertEquals($expectedTree, $tree);
    }
}
