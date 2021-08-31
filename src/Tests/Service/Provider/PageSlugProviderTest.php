<?php

namespace Karkov\Kcms\Tests\Service\Provider;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Entity\Site;
use Karkov\Kcms\Repository\PageSlugRepository;
use Karkov\Kcms\Repository\SiteRepository;
use Karkov\Kcms\Service\Provider\PageSlugProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

class PageSlugProviderTest extends TestCase
{
    public function testBuildNewPageSlug()
    {
        // Given
        $pageSlugRepositoryMock = $this->createMock(PageSlugRepository::class);
        $siteRepositoryMock = $this->createMock(SiteRepository::class);
        $cacheMock = $this->createMock(CacheInterface::class);
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $pageSlugPovider = new PageSlugProvider($pageSlugRepositoryMock, $siteRepositoryMock, $cacheMock, $config);
        $page = new Page();

        // When
        $pageSlug = $pageSlugPovider->provideNewPageSlug('my-slug', 'fr_FR', $page);

        // Then
        $expected = new PageSlug();
        $expected
            ->setSlug('/my-slug')
            ->setLocal('fr_FR')
            ->setPage($page)
        ;

        $this->assertEquals($expected, $pageSlug);
    }

    public function testGetPageSlugWithSiteNull()
    {
        // Given
        $pageSlugRepositoryMock = $this->createMock(PageSlugRepository::class);
        $siteRepositoryMock = $this->createMock(SiteRepository::class);
        $site = new Site();
        $site->setIsEnable(false);

        $siteRepositoryMock->method('findOneBy')->willReturn($site);
        $cacheMock = $this->createMock(CacheInterface::class);
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $pageSlugPovider = new PageSlugProvider($pageSlugRepositoryMock, $siteRepositoryMock, $cacheMock, $config);
        $dto = new RequestDto();

        // When
        $pageSlug = $pageSlugPovider->getPageSlug($dto);

        // Then
        $this->assertNull($pageSlug);
    }

    public function testGetPageSlugWithNoUrlMatcher()
    {
        // Given
        $page = new Page();
        $pageSlug01 = new PageSlug();
        $pageSlug01
            ->setLocal('fr_FR')
            ->setSlug('/my-slug')
            ->setPage($page)
        ;

        $pageSlug02 = new PageSlug();
        $pageSlug02
            ->setLocal('fr_FR')
            ->setSlug('/another-slug')
            ->setPage($page)
        ;

        $pageSlugRepositoryMock = $this->createMock(PageSlugRepository::class);
        $pageSlugRepositoryMock->method('findAllSlugsBySiteAndLocal')->willReturn([$pageSlug01, $pageSlug02]);

        $siteRepositoryMock = $this->createMock(SiteRepository::class);
        $site = new Site();
        $site
            ->setDomain('domain.net')
            ->setIsEnable(true)
        ;
        $siteRepositoryMock->method('findOneBy')->willReturn($site);

        $cache = new ArrayAdapter();
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $pageSlugPovider = new PageSlugProvider($pageSlugRepositoryMock, $siteRepositoryMock, $cache, $config);
        $dto = new RequestDto();
        $request = new Request();
        $dto->setRequest($request);

        // When
        $pageSlug = $pageSlugPovider->getPageSlug($dto);

        // Then
        $this->assertNull($pageSlug);
    }

    public function testGetPageSlugWithLocal()
    {
        // Given
        $page = new Page();
        $pageSlug01 = new PageSlug();
        $pageSlug01
            ->setLocal('fr_FR')
            ->setSlug('/')
            ->setPage($page)
        ;

        $pageSlug02 = new PageSlug();
        $pageSlug02
            ->setLocal('fr_FR')
            ->setSlug('/another-slug')
            ->setPage($page)
        ;

        $pageSlugRepositoryMock = $this->createMock(PageSlugRepository::class);
        $pageSlugRepositoryMock->method('findAllSlugsBySiteAndLocal')->willReturn([$pageSlug01, $pageSlug02]);

        $siteRepositoryMock = $this->createMock(SiteRepository::class);
        $site = new Site();
        $site
            ->setDomain('domain.net')
            ->setIsEnable(true)
        ;
        $siteRepositoryMock->method('findOneBy')->willReturn($site);

        $cache = new ArrayAdapter();
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $pageSlugPovider = new PageSlugProvider($pageSlugRepositoryMock, $siteRepositoryMock, $cache, $config);
        $dto = new RequestDto();
        $request = Request::create(
            '/fr_FR/another-slug',
            'GET'
        );
        $request->setLocale('fr_FR');
        $dto->setRequest($request);

        // When
        $pageSlug = $pageSlugPovider->getPageSlug($dto);

        // Then
        $expected = new PageSlug();
        $expected
            ->setPage($page)
            ->setSlug('/another-slug')
            ->setLocal('fr_FR')
        ;
        $expected->setRouteAttributes(['pageSlug' => $expected, '_route' => '/another-slug', '_local' => 'fr_FR']);
        $this->assertEquals($expected, $pageSlug);
    }

    public function testGetPageSlugWithoutLocal()
    {
        // Given
        $page = new Page();
        $pageSlug01 = new PageSlug();
        $pageSlug01
            ->setLocal('fr_FR')
            ->setSlug('/')
            ->setPage($page)
        ;

        $pageSlug02 = new PageSlug();
        $pageSlug02
            ->setLocal('fr_FR')
            ->setSlug('/another-slug')
            ->setPage($page)
        ;

        $pageSlugRepositoryMock = $this->createMock(PageSlugRepository::class);
        $pageSlugRepositoryMock->method('findAllSlugsBySiteAndLocal')->willReturn([$pageSlug01, $pageSlug02]);

        $siteRepositoryMock = $this->createMock(SiteRepository::class);
        $site = new Site();
        $site
            ->setDomain('domain.net')
            ->setIsEnable(true)
        ;
        $siteRepositoryMock->method('findOneBy')->willReturn($site);

        $cache = new ArrayAdapter();
        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => false,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];

        $pageSlugPovider = new PageSlugProvider($pageSlugRepositoryMock, $siteRepositoryMock, $cache, $config);
        $dto = new RequestDto();
        $request = Request::create(
            '/another-slug',
            'GET'
        );
        $request->setLocale('fr_FR');
        $dto->setRequest($request);

        // When
        $pageSlug = $pageSlugPovider->getPageSlug($dto);

        // Then
        $expected = new PageSlug();
        $expected
            ->setPage($page)
            ->setSlug('/another-slug')
            ->setLocal('fr_FR')
        ;
        $expected->setRouteAttributes(['pageSlug' => $expected, '_route' => '/another-slug']);
        $this->assertEquals($expected, $pageSlug);
    }
}
