<?php

namespace Karkov\Kcms\Tests\Service\Provider;

use Karkov\Kcms\Dto\KcmsDto;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageContent;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Exception\ModuleCannotBeAutowiredException;
use Karkov\Kcms\Exception\ModuleNotFoundException;
use Karkov\Kcms\Modules\TextModule;
use Karkov\Kcms\Service\Helper\DateTimer;
use Karkov\Kcms\Service\Provider\KcmsDtoProvider;
use Karkov\Kcms\Tests\Service\Provider\UserMock as User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class KcmsDtoProviderTest extends KernelTestCase
{
    public function testGetKcmsDtoWithNoPageMatchingRequest()
    {
        // Given
        $cache = new ArrayAdapter();
        $containerMock = $this->createMock(Container::class);
        $twigMock = $this->createMock(Environment::class);
        $security = $this->createMock(Security::class);

        $config = [
            'zones' => ['nb' => 10],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];
        $kcmsDtoProvider = new KcmsDtoProvider($cache, $containerMock, $twigMock, $security, $config);

        $request = Request::create(
            '/a-slug',
            'GET'
        );
        $request->setLocale('fr_FR');

        // When
        $requestDto = new RequestDto();
        $requestDto
            ->setRequest($request)
            ->setLocal('fr_FR')
            ->setHost('domain.net')
        ;

        $kcmsDto = $kcmsDtoProvider->provideKcmsDto($requestDto);

        // Then
        $expected = new KcmsDto();
        $expected->setZones(['', '', '', '', '', '', '', '', '', '']);
        $expected->setRequestDto($requestDto);

        $this->assertEquals($expected, $kcmsDto);
    }

    public function testGetKcmsDtoWithAPageMatchingRequest()
    {
        // Given
        $cache = new ArrayAdapter();
        $containerMock = $this->createMock(Container::class);
        $containerMock->method('get')->willThrowException(new ServiceNotFoundException('idModule'));

        static::bootKernel();
        $twig = self::$container->get('twig');

        $user = new User();
        $user->setRoles(['ROLE_ADMIN_KCMS']);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $config = [
            'zones' => ['nb' => 6],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];
        $kcmsDtoProvider = new KcmsDtoProvider($cache, $containerMock, $twig, $security, $config);

        $request = Request::create(
            '/a-slug',
            'GET'
        );
        $request->setLocale('fr_FR');

        // First content on zone 4
        $pageContent01 = new PageContent();
        $pageContent01
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
            ->setRank(1)
            ->setZone(4)
        ;

        $contentLocal01 = new ContentLocal();
        $contentLocal01
            ->setLocal('fr_FR')
            ->setRawContent('Hello World')
        ;

        $content01 = new Content();
        $content01
            ->setTitle('Hello')
            ->setModule(TextModule::class)
            ->addContentLocal($contentLocal01)
        ;

        $content01->addPageContent($pageContent01);

        // Second content, on the same zone in order to test the sort function
        $pageContent02 = new PageContent();
        $pageContent02
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
            ->setRank(2)
            ->setZone(4)
        ;

        $contentLocal02 = new ContentLocal();
        $contentLocal02
            ->setLocal('fr_FR')
            ->setRawContent('another content here')
        ;

        $content02 = new Content();
        $content02
            ->setTitle('Hello')
            ->setModule(TextModule::class)
            ->addContentLocal($contentLocal02)
        ;

        $content02->addPageContent($pageContent02);

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setSlug('/a-slug')
        ;

        $page = new Page();
        $page
            ->setTitle('a page')
            ->setTemplate('template.html.twig')
            ->addPageContent($pageContent01)
            ->addPageContent($pageContent02)
            ->addPageSlug($pageSlug)
        ;

        $requestDto = new RequestDto();
        $requestDto
            ->setRequest($request)
            ->setLocal('fr_FR')
            ->setHost('domain.net')
            ->setPageSlug($pageSlug)
        ;

        // When
        $kcmsDto = $kcmsDtoProvider->provideKcmsDto($requestDto);

        // Then
        $expected = new KcmsDto();
        $expected->setZones([
            '<section class="kcms_zone kcms_zone_0" data-zone="0"></section>
',
            '<section class="kcms_zone kcms_zone_1" data-zone="1"></section>
',
            '<section class="kcms_zone kcms_zone_2" data-zone="2"></section>
',
            '<section class="kcms_zone kcms_zone_3" data-zone="3"></section>
',
            '<section class="kcms_zone kcms_zone_4" data-zone="4"><section id="0a4d55a8d778e5022fab701977c5d840bbc486d0" data-content-id="" class="kcms_content kcms_content_">Hello World</section>
<section id="ba1b916900ea101789affd1a6330758625405f4d" data-content-id="" class="kcms_content kcms_content_">another content here</section>
</section>
',
            '<section class="kcms_zone kcms_zone_5" data-zone="5"></section>
',
        ]);
        $expected->setRequestDto($requestDto);
        $expected->setPage($page);
        $expected->setJs('<script>
    const BUILD_KCMS_MENU = true;
    const KCMS_PAGE_ID=;
</script>');

        $this->assertEquals($expected, $kcmsDto);
    }

    public function testGetKcmsDtoWithAPrivateModuleAndWithArgumentOnConstructor()
    {
        // Given
        $cache = new ArrayAdapter();
        $containerMock = $this->createMock(Container::class);
        $containerMock->method('get')->willThrowException(new ServiceNotFoundException('idModule'));
        $twigMock = $this->createMock(Environment::class);

        $user = new User();
        $user->setRoles(['ROLE_ADMIN_KCMS']);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $config = [
            'zones' => ['nb' => 6],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];
        $kcmsDtoProvider = new KcmsDtoProvider($cache, $containerMock, $twigMock, $security, $config);

        $request = Request::create(
            '/a-slug',
            'GET'
        );
        $request->setLocale('fr_FR');

        $pageContent = new PageContent();
        $pageContent
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
            ->setRank(1)
            ->setZone(4)
        ;

        $contentLocal = new ContentLocal();
        $contentLocal
            ->setLocal('fr_FR')
            ->setRawContent('Hello World')
        ;

        $content = new Content();
        $content
            ->setTitle('Hello')
            ->setModule(ModuleMock::class)
            ->addContentLocal($contentLocal)
        ;

        $content->addPageContent($pageContent);

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setSlug('/a-slug')
        ;

        $page = new Page();
        $page
            ->setTitle('a page')
            ->setTemplate('template.html.twig')
            ->addPageContent($pageContent)
            ->addPageSlug($pageSlug)
        ;

        $requestDto = new RequestDto();
        $requestDto
            ->setRequest($request)
            ->setLocal('fr_FR')
            ->setHost('domain.net')
            ->setPageSlug($pageSlug)
        ;

        // Then
        $this->expectException(ModuleCannotBeAutowiredException::class);
        $this->expectExceptionMessage('The constructor of the module Karkov\Kcms\Tests\Service\Provider\ModuleMock have some arguments, but it cannot be autowired because it is not present on the service container. You should explicitly set this module as a public service');

        // When
        $kcmsDtoProvider->provideKcmsDto($requestDto);
    }

    public function testGetKcmsDtoWithAnUnexistingModuleAttachedToContent()
    {
        // Given
        $cache = new ArrayAdapter();
        $containerMock = $this->createMock(Container::class);
        $containerMock->method('get')->willThrowException(new ServiceNotFoundException('idModule'));
        $twigMock = $this->createMock(Environment::class);

        $user = new User();
        $user->setRoles(['ROLE_ADMIN_KCMS']);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $config = [
            'zones' => ['nb' => 6],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];
        $kcmsDtoProvider = new KcmsDtoProvider($cache, $containerMock, $twigMock, $security, $config);

        $request = Request::create(
            '/a-slug',
            'GET'
        );
        $request->setLocale('fr_FR');

        $pageContent = new PageContent();
        $pageContent
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
            ->setRank(1)
            ->setZone(4)
        ;

        $contentLocal = new ContentLocal();
        $contentLocal
            ->setLocal('fr_FR')
            ->setRawContent('Hello World')
        ;

        $content = new Content();
        $content
            ->setTitle('Hello')
            ->setModule('UnknowModule')
            ->addContentLocal($contentLocal)
        ;

        $content->addPageContent($pageContent);

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setSlug('/a-slug')
        ;

        $page = new Page();
        $page
            ->setTitle('a page')
            ->setTemplate('template.html.twig')
            ->addPageContent($pageContent)
            ->addPageSlug($pageSlug)
        ;

        $requestDto = new RequestDto();
        $requestDto
            ->setRequest($request)
            ->setLocal('fr_FR')
            ->setHost('domain.net')
            ->setPageSlug($pageSlug)
        ;

        // Then
        $this->expectException(ModuleNotFoundException::class);

        // When
        $kcmsDtoProvider->provideKcmsDto($requestDto);
    }

    public function testGetKcmsDtoWithNoContentLocalizedAttachedToContent()
    {
        // Given
        $cache = new ArrayAdapter();
        $containerMock = $this->createMock(Container::class);
        $containerMock->method('get')->willThrowException(new ServiceNotFoundException('idModule'));

        static::bootKernel();
        $twig = self::$container->get('twig');

        $security = $this->createMock(Security::class);

        $config = [
            'zones' => ['nb' => 6],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];
        $kcmsDtoProvider = new KcmsDtoProvider($cache, $containerMock, $twig, $security, $config);

        $request = Request::create(
            '/a-slug',
            'GET'
        );
        $request->setLocale('fr_FR');

        $pageContent = new PageContent();
        $pageContent
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
            ->setRank(1)
            ->setZone(4)
        ;

        $content = new Content();
        $content
            ->setTitle('Hello')
            ->setModule(TextModule::class)
        ;

        $content->addPageContent($pageContent);

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setSlug('/a-slug')
        ;

        $page = new Page();
        $page
            ->setTitle('a page')
            ->setTemplate('template.html.twig')
            ->addPageContent($pageContent)
            ->addPageSlug($pageSlug)
        ;

        $requestDto = new RequestDto();
        $requestDto
            ->setRequest($request)
            ->setLocal('fr_FR')
            ->setHost('domain.net')
            ->setPageSlug($pageSlug)
        ;

        // When
        $kcmsDto = $kcmsDtoProvider->provideKcmsDto($requestDto);

        // Then
        $expected = new KcmsDto();
        $expected->setZones([
            '<section class="kcms_zone kcms_zone_0" data-zone="0"></section>
',
            '<section class="kcms_zone kcms_zone_1" data-zone="1"></section>
',
            '<section class="kcms_zone kcms_zone_2" data-zone="2"></section>
',
            '<section class="kcms_zone kcms_zone_3" data-zone="3"></section>
',
            '<section class="kcms_zone kcms_zone_4" data-zone="4"></section>
',
            '<section class="kcms_zone kcms_zone_5" data-zone="5"></section>
',
        ]);
        $expected->setRequestDto($requestDto);
        $expected->setPage($page);

        $this->assertEquals($expected, $kcmsDto);
    }

    public function testGetKcmsDtoWithANotCacheableModule()
    {
        // Given
        $cache = new ArrayAdapter();
        $containerMock = $this->createMock(Container::class);
        $containerMock->method('get')->willThrowException(new ServiceNotFoundException('idModule'));

        static::bootKernel();
        $twig = self::$container->get('twig');

        $security = $this->createMock(Security::class);

        $config = [
            'zones' => ['nb' => 6],
            'multilingual' => [
                'enable' => true,
                'local' => ['en_UK', 'fr_FR'],
            ],
        ];
        $kcmsDtoProvider = new KcmsDtoProvider($cache, $containerMock, $twig, $security, $config);

        $request = Request::create(
            '/a-slug',
            'GET'
        );
        $request->setLocale('fr_FR');

        $pageContent = new PageContent();
        $pageContent
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2050-01-01'))
            ->setRank(1)
            ->setZone(4)
        ;

        $contentLocal = new ContentLocal();
        $contentLocal
            ->setLocal('fr_FR')
            ->setRawContent('Hello World')
        ;

        $content = new Content();
        $content
            ->setTitle('Hello')
            ->setModule(NotCacheableModuleMock::class)
            ->addContentLocal($contentLocal)
        ;

        $content->addPageContent($pageContent);

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setSlug('/a-slug')
        ;

        $page = new Page();
        $page
            ->setTitle('a page')
            ->setTemplate('template.html.twig')
            ->addPageContent($pageContent)
            ->addPageSlug($pageSlug)
        ;

        $requestDto = new RequestDto();
        $requestDto
            ->setRequest($request)
            ->setLocal('fr_FR')
            ->setHost('domain.net')
            ->setPageSlug($pageSlug)
        ;

        // When
        $kcmsDto = $kcmsDtoProvider->provideKcmsDto($requestDto);

        // Then
        $expected = new KcmsDto();
        $expected->setZones([
            '<section class="kcms_zone kcms_zone_0" data-zone="0"></section>
',
            '<section class="kcms_zone kcms_zone_1" data-zone="1"></section>
',
            '<section class="kcms_zone kcms_zone_2" data-zone="2"></section>
',
            '<section class="kcms_zone kcms_zone_3" data-zone="3"></section>
',
            '<section class="kcms_zone kcms_zone_4" data-zone="4"><section id="0a4d55a8d778e5022fab701977c5d840bbc486d0" data-content-id="" class="kcms_content kcms_content_">Hello World</section>
</section>
',
            '<section class="kcms_zone kcms_zone_5" data-zone="5"></section>
',
        ]);
        $expected->setRequestDto($requestDto);
        $expected->setPage($page);

        $this->assertEquals($expected, $kcmsDto);
    }
}
