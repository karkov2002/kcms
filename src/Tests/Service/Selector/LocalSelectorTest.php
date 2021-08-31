<?php

namespace Karkov\Kcms\Tests\Service\Selector;

use Karkov\Kcms\Service\Selector\LocalSelector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LocalSelectorTest extends TestCase
{
    public function testGetListWhenMultilingueIsEnable()
    {
        // Given
        $request = Request::create('/');
        $request->setLocale('fr_FR');
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getMasterRequest')->willReturn($request);
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $localSelector = new LocalSelector($requestStackMock, $config);

        // When
        $list = $localSelector->getList();

        // Then
        $expected = [
            'en_UK' => 'en_UK',
            'fr_FR' => 'fr_FR',
        ];
        $this->assertEquals($expected, $list);
    }

    public function testGetListWhenMultilingueIsDisable()
    {
        // Given
        $request = Request::create('/');
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock->method('getMasterRequest')->willReturn($request);
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => false,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $localSelector = new LocalSelector($requestStackMock, $config);

        // When
        $list = $localSelector->getList();

        // Then
        $expected = [
            'default' => 'en',
        ];
        $this->assertEquals($expected, $list);
    }
}
