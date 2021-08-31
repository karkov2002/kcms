<?php

namespace Karkov\Kcms\Tests\Service\Builder;

use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Service\Builder\NewHtmlPatternBuilder;
use PHPUnit\Framework\TestCase;

class NewHtmlPatternBuilderTest extends TestCase
{
    public function testBuild()
    {
        // Given
        $newHtmlPatternBuilder = new NewHtmlPatternBuilder();
        $jsTreeCreateNodeDto = new JsTreeCreateNodeDto();
        $jsTreeCreateNodeDto->type = 'node';
        $jsTreeCreateNodeDto->typeTree = 'patternhtml';
        $jsTreeCreateNodeDto->label = 'a new html pattern';

        $parentTree = new Tree();
        $parentTree
            ->setName('root')
            ->setType('patternhtml')
        ;

        // When
        $result = $newHtmlPatternBuilder->build($jsTreeCreateNodeDto, $parentTree);

        // Then
        $expectedHtmlPattern = new HtmlPattern();
        $expectedHtmlPattern
            ->setTitle('a new html pattern')
            ->setParent($parentTree)
        ;
        $this->assertEquals($expectedHtmlPattern, $result);
    }
}
