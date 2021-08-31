<?php

namespace Karkov\Kcms\Tests\EventSubscriber;

use Karkov\Kcms\Controller\KcmsController;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\EventSubscriber\KernelRequestSubscriber;
use Karkov\Kcms\Service\Provider\RequestDtoProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class KernelRequestSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents()
    {
        $expected = [KernelEvents::REQUEST => ['onKernelRequest', 256]];
        $this->assertEquals($expected, KernelRequestSubscriber::getSubscribedEvents());
    }

    public function testOnKernelRequest()
    {
        // Given
        $request = new Request();

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setSlug('/')
        ;
        $requestDto = new RequestDto();
        $requestDto
            ->setLocal('fr_FR')
            ->setHost('test.net')
            ->setPageSlug($pageSlug)
            ->setRequest($request)
        ;

        $requestDtoProviderMock = $this->createMock(RequestDtoProvider::class);
        $requestDtoProviderMock->method('provideRequestDtoFromRequest')->willReturn($requestDto);

        $routeCollection = new RouteCollection();
        $routeInterfaceMock = $this->createMock(RouterInterface::class);
        $routeInterfaceMock->method('getRouteCollection')->willReturn($routeCollection);

        $requestEventMock = $this->createMock(RequestEvent::class);
        $requestEventMock->method('isMasterRequest')->willReturn(true);
        $requestEventMock->method('getRequest')->willReturn($request);

        $kernelRequestSubscriber = new KernelRequestSubscriber($requestDtoProviderMock, $routeInterfaceMock);

        // When
        $kernelRequestSubscriber->onKernelRequest($requestEventMock);

        // Then
        $this->assertEquals('fr_FR', $request->getLocale());
        $this->assertEquals(KcmsController::class, $request->attributes->get('_controller'));
        $this->assertEquals('fr_FR', $request->attributes->get('_local'));
    }

    public function testOnKernelRequestWithASubRequest()
    {
        // Given
        $request = new Request();

        $pageSlug = new PageSlug();
        $pageSlug
            ->setLocal('fr_FR')
            ->setSlug('/')
        ;
        $requestDto = new RequestDto();
        $requestDto
            ->setLocal('fr_FR')
            ->setHost('test.net')
            ->setPageSlug($pageSlug)
            ->setRequest($request)
        ;

        $requestDtoProviderMock = $this->createMock(RequestDtoProvider::class);
        $requestDtoProviderMock->method('provideRequestDtoFromRequest')->willReturn($requestDto);

        $routeInterfaceMock = $this->createMock(RouterInterface::class);

        $requestEventMock = $this->createMock(RequestEvent::class);
        $requestEventMock->method('isMasterRequest')->willReturn(false);

        $kernelRequestSubscriber = new KernelRequestSubscriber($requestDtoProviderMock, $routeInterfaceMock);

        // When
        $kernelRequestSubscriber->onKernelRequest($requestEventMock);

        // Then
        $this->assertEquals('en', $request->getLocale());
        $this->assertNull($request->attributes->get('_controller'));
    }
}
