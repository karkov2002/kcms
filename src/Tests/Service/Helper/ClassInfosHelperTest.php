<?php

namespace Karkov\Kcms\Tests\Service\Helper;

use Karkov\Kcms\Service\Helper\ClassInfosHelper;
use PHPUnit\Framework\TestCase;

class ClassInfosHelperTest extends TestCase
{
    public function testGetClassInfosFromFile()
    {
        // Given
        $classInfosHelper = new ClassInfosHelper();
        $file = __DIR__.'/../../../Service/Helper/ClassInfosHelper.php';

        // When
        $infos = $classInfosHelper->getClassInfosFromFile($file);

        // Then
        $expected = [
            'namespace' => '\Karkov\Kcms\Service\Helper',
            'class' => 'ClassInfosHelper',
            'interface' => false,
            'abstract' => false,
        ];

        $this->assertEquals($expected, $infos);

        // Given
        $classInfosHelper = new ClassInfosHelper();
        $file = __DIR__.'/../../../Controller/JsTree/JsTreeController.php';

        // When
        $infos = $classInfosHelper->getClassInfosFromFile($file);

        // Then
        $expected = [
            'namespace' => '\Karkov\Kcms\Controller\JsTree',
            'class' => 'JsTreeController',
            'interface' => false,
            'abstract' => false,
        ];

        $this->assertEquals($expected, $infos);

        // Given
        $classInfosHelper = new ClassInfosHelper();
        $file = __DIR__.'/../../../Modules/AbstractModule.php';

        // When
        $infos = $classInfosHelper->getClassInfosFromFile($file);

        // Then
        $expected = [
            'namespace' => '\Karkov\Kcms\Modules',
            'class' => 'AbstractModule',
            'interface' => false,
            'abstract' => true,
        ];

        $this->assertEquals($expected, $infos);

        // Given
        $classInfosHelper = new ClassInfosHelper();
        $file = __DIR__.'/../../../Modules/KcmsModuleInterface.php';

        // When
        $infos = $classInfosHelper->getClassInfosFromFile($file);

        $this->assertEquals(true, $infos['interface']);
    }
}
