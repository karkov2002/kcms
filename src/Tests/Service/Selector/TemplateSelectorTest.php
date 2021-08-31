<?php

namespace Karkov\Kcms\Tests\Service\Selector;

use Karkov\Kcms\Service\Selector\TemplateSelector;
use PHPUnit\Framework\TestCase;

class TemplateSelectorTest extends TestCase
{
    public function testGetList()
    {
        $templateSelector = new TemplateSelector(__DIR__);

        $list = $templateSelector->getList();

        $expected = [
            '@KcmsBundle/default/kcms.default.html.twig' => '@KcmsBundle/default/kcms.default.html.twig',
        ];
        $this->assertEquals($expected, $list);
    }
}
