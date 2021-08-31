<?php

namespace Karkov\Kcms\Tests\ArgumentResolver;

use Karkov\Kcms\ArgumentResolver\KcmsResolver;
use Karkov\Kcms\Dto\KcmsDto;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Service\Provider\KcmsDtoProvider;
use Karkov\Kcms\Service\Provider\RequestDtoProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class KcmsResolverTest extends TestCase
{
    public function testSupports()
    {
        $requestDtoProvider = $this->createMock(RequestDtoProvider::class);
        $kcmsDtoProvider = $this->createMock(KcmsDtoProvider::class);

        $kcmsResolver = new KcmsResolver($requestDtoProvider, $kcmsDtoProvider);

        $request = Request::create('/');
        $argument = new ArgumentMetadata('kcms', KcmsDto::class, false, false, null, false, null);

        $this->assertTrue($kcmsResolver->supports($request, $argument));
    }

    public function testResolveWhenRequestDtoIsNull()
    {
        $requestDtoProvider = $this->createMock(RequestDtoProvider::class);
        $requestDtoProvider->method('provideRequestDtoFromRequest')->willReturn(null);

        $kcmsDtoProvider = $this->createMock(KcmsDtoProvider::class);

        $kcmsResolver = new KcmsResolver($requestDtoProvider, $kcmsDtoProvider);

        $request = Request::create('/');
        $argument = new ArgumentMetadata('kcms', KcmsDto::class, false, false, null, false, null);

        $results = $kcmsResolver->resolve($request, $argument);

        $this->assertIsIterable($results);

        foreach ($results as $result) {
            $this->assertEquals(new KcmsDto(), $result);
        }
    }

    public function testResolveWhenRequestDtoIsNotNull()
    {
        $pageSlug = new PageSlug();
        $pageSlug
            ->setSlug('/a-slug')
            ->setLocal('fr_FR')
        ;

        $requestDto = new RequestDto();
        $requestDto
            ->setLocal('fr_FR')
            ->setHost('domain.net')
            ->setPageSlug($pageSlug)
        ;

        $requestDtoProvider = $this->createMock(RequestDtoProvider::class);
        $requestDtoProvider->method('provideRequestDtoFromRequest')->willReturn($requestDto);

        $page = new Page();
        $page->setTitle('a page');
        $kcmsDto = new KcmsDto();
        $kcmsDto->setPage($page);

        $kcmsDtoProvider = $this->createMock(KcmsDtoProvider::class);
        $kcmsDtoProvider->method('provideKcmsDto')->willReturn($kcmsDto);

        $kcmsResolver = new KcmsResolver($requestDtoProvider, $kcmsDtoProvider);

        $request = Request::create('/a-slug');
        $argument = new ArgumentMetadata('kcms', KcmsDto::class, false, false, null, false, null);

        $results = $kcmsResolver->resolve($request, $argument);

        $this->assertIsIterable($results);

        foreach ($results as $result) {
            $this->assertEquals($kcmsDto, $result);
        }
    }
}
