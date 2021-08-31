<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Form\EditContent\ContentLocalTextType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ControllerModule extends AbstractModule
{
    private $requestStack;
    private $container;

    public function __construct(RequestStack $requestStack, ContainerInterface $container)
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    public function getContent(RequestDto $requestDto)
    {
        $request = Request::createFromGlobals();
        $request->attributes->set('_controller', $this->rawContent);

        $httpKernel = $this->container->get('http_kernel');
        $response = $httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST);

        return $response->getContent();
    }

    public function isCacheable(): bool
    {
        return true;
    }

    public function getCacheKey(RequestDto $requestDto): string
    {
        return sha1(self::class.$this->rawContent);
    }

    public static function getFormType(): string
    {
        return ContentLocalTextType::class;
    }
}
