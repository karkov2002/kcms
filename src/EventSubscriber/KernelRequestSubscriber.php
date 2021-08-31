<?php

namespace Karkov\Kcms\EventSubscriber;

use Karkov\Kcms\Controller\KcmsController;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Service\Provider\RequestDtoProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class KernelRequestSubscriber implements EventSubscriberInterface
{
    private $router;
    private $requestDtoProvider;

    public function __construct(RequestDtoProvider $requestDtoProvider, RouterInterface $router)
    {
        $this->router = $router;
        $this->requestDtoProvider = $requestDtoProvider;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $requestDto = $this->requestDtoProvider->provideRequestDtoFromRequest($request);

        if (null === $requestDto || null === $requestDto->getPageSlug()) {
            return;
        }

        if ($this->isThereNoRouteAttachedToAControllerForThisRequest($request)) {
            $this->forceRequestToUseKcmsController($request, $requestDto);
        }
    }

    private function isThereNoRouteAttachedToAControllerForThisRequest(Request $request): bool
    {
        // If there is a kcms page attached to this slug
        // Determine if a route also exists on symfony app that match the current request
        // If yes : the symfony controller attached to the route must continue to be used
        // If not : the kcms controller is used in order to build and return the kcms page
        $matcher = new UrlMatcher($this->router->getRouteCollection(), new RequestContext());

        try {
            $matcher->matchRequest($request);

            return false;
        } catch (ResourceNotFoundException $exception) {
            return true;
        }
    }

    private function forceRequestToUseKcmsController(Request $request, RequestDto $requestDto): void
    {
        $request->attributes->set('_controller', KcmsController::class);
        $request->attributes->set('_local', $requestDto->getLocal());

        if (null !== $requestDto->getLocal()) {
            $request->setLocale($requestDto->getLocal());
        }
    }
}
