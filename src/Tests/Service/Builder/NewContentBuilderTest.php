<?php

namespace Karkov\Kcms\Tests\Service\Builder;

use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Modules\TextModule;
use Karkov\Kcms\Service\Builder\NewContentBuilder;
use PHPUnit\Framework\TestCase;

class NewContentBuilderTest extends TestCase
{
    public function testBuild()
    {
        // Given
        $newContentBuilder = new NewContentBuilder();
        $tree = new Tree();
        $tree
            ->setType('content')
            ->setName('root')
        ;

        $dto = new JsTreeCreateNodeDto();
        $dto->label = 'a new content';
        $dto->typeTree = 'content';
        $dto->type = 'node';

        // When
        $content = $newContentBuilder->build($dto, $tree);

        //Then
        $contentExpected = new Content();
        $contentExpected
            ->setTitle('a new content')
            ->setParent($tree)
            ->setModule(TextModule::class)
        ;

        $this->assertEquals($contentExpected, $content);
    }
}
