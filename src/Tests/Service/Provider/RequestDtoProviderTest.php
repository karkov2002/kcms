<?php

namespace Karkov\Kcms\Tests\Service\Provider;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Service\Provider\PageSlugProvider;
use Karkov\Kcms\Service\Provider\RequestDtoProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;

class RequestDtoProviderTest extends TestCase
{
    public function testGetRequestDtoFromRequestIsNull()
    {
        // Given
        $pageSlugProviderMock = $this->createMock(PageSlugProvider::class);
        $cacheMock = new ArrayAdapter();
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $requestDtoProvider = new RequestDtoProvider($pageSlugProviderMock, $cacheMock, $config);
        $request = new Request();

        // When
        $requestDto = $requestDtoProvider->provideRequestDtoFromRequest($request);

        // Then
        $this->assertNull($requestDto);
    }

    public function testGetRequestDtoFromRequestWithoutMultilingue()
    {
        // Given
        $pageSlugProviderMock = $this->createMock(PageSlugProvider::class);
        $cacheMock = new ArrayAdapter();
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => false,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $requestDtoProvider = new RequestDtoProvider($pageSlugProviderMock, $cacheMock, $config);
        $request = new Request();

        // When
        $requestDto = $requestDtoProvider->provideRequestDtoFromRequest($request);

        // Then
        $expected = new RequestDto();
        $expected
            ->setRequest($request)
            ->setHost('')
            ->setLocal('en');
        $this->assertEquals($expected, $requestDto);
    }

    public function testGetRequestDtoFromRequestWithMultilingue()
    {
        // Given
        $pageSlugProviderMock = $this->createMock(PageSlugProvider::class);
        $cacheMock = new ArrayAdapter();
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $requestDtoProvider = new RequestDtoProvider($pageSlugProviderMock, $cacheMock, $config);

        $request = Request::create('/fr_FR/hello-world', 'GET');
        $request->setLocale('fr_FR');

        // When
        $requestDto = $requestDtoProvider->provideRequestDtoFromRequest($request);

        // Then
        $expected = new RequestDto();
        $expected
            ->setRequest($request)
            ->setHost('localhost')
            ->setLocal('fr_FR');

        $this->assertEquals($expected, $requestDto);
    }
}
