<?php

namespace Karkov\Kcms\Tests\Dto;

use Karkov\Kcms\Dto\KcmsDto;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageSlug;
use PHPUnit\Framework\TestCase;

class KcmsDtoTest extends TestCase
{
    public function testKcmsDto()
    {
        $page = new Page();
        $page->setTitle('a page');

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setPage($page)
            ->setSlug('/a-slug')
        ;

        $requestDto = new RequestDto();
        $requestDto
            ->setLocal('fr_FR')
            ->setHost('domain.net')
            ->setPageSlug($pageSlug)
        ;

        $kcmsDto = new KcmsDto();
        $kcmsDto
            ->setRequestDto($requestDto)
            ->setPage($page)
            ->setZones(['z1', 'z2', 'z3'])
            ->setJs('<script>console.log(\'debug\')</script>')
        ;

        $this->assertEquals($page, $kcmsDto->getPage());
        $this->assertEquals('<script>console.log(\'debug\')</script>', $kcmsDto->getJs());
        $this->assertEquals(['z1', 'z2', 'z3'], $kcmsDto->getZones());
        $this->assertEquals($requestDto, $kcmsDto->getRequestDto());

        $this->assertEquals('host : domain.net, local : fr_FR, slug : /a-slug', (string) $requestDto);
    }
}
