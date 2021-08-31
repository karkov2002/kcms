<?php

namespace Karkov\Kcms\Tests\Service\Selector;

use Karkov\Kcms\Service\Helper\ClassInfosHelper;
use Karkov\Kcms\Service\Selector\ModuleSelector;
use PHPUnit\Framework\TestCase;

class ModuleSelectorTest extends TestCase
{
    public function testGetList()
    {
        // Given
        $moduleSelector = new ModuleSelector(__DIR__.'/../../../..', new ClassInfosHelper());

        // When
        $list = $moduleSelector->getList();

        // Then
        $expected = [
            'Karkov\Kcms\Modules\HtmlModule' => 'Karkov\Kcms\Modules\HtmlModule',
            'Karkov\Kcms\Modules\ControllerModule' => 'Karkov\Kcms\Modules\ControllerModule',
            'Karkov\Kcms\Modules\TextModule' => 'Karkov\Kcms\Modules\TextModule',
            'Karkov\Kcms\Modules\TextAreaModule' => 'Karkov\Kcms\Modules\TextAreaModule',
            'Karkov\Kcms\Modules\HtmlLightModule' => 'Karkov\Kcms\Modules\HtmlLightModule',
            'Karkov\Kcms\Modules\ComposedModule' => 'Karkov\Kcms\Modules\ComposedModule',
        ];
        $this->assertEquals($expected, $list);
    }
}
