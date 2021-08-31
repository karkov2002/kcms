<?php

namespace Karkov\Kcms\Service\Provider;

use Karkov\Kcms\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

class RequestDtoProvider
{
    private $config;
    private $pageSlugProvider;
    private $requestDto;
    private $cache;

    public function __construct(
        PageSlugProvider $pageSlugProvider,
        CacheInterface $cache,
        array $config
    ) {
        $this->config = $config;
        $this->pageSlugProvider = $pageSlugProvider;
        $this->cache = $cache;
    }

    public function provideRequestDtoFromRequest(Request $request): ?RequestDto
    {
        if (null === $this->requestDto) {
            $cacheKey = $this->getCacheKeyFromRequest($request);
            $this->requestDto = $this->cache->get($cacheKey, function () use ($request) {
                $local = $request->getLocale();
                $host = $request->getHost();

                if (true === $this->config['multilingual']['enable']) {
                    $parts = explode('/', trim($request->getPathInfo(), '/'));
                    $local = $parts[0];

                    if (!in_array($local, $this->config['multilingual']['local'], true)) {
                        return null;
                    }
                }

                $requestDto = new RequestDto();
                $requestDto
                    ->setHost($host)
                    ->setLocal($local)
                    ->setRequest($request)
                ;

                $pageSlug = $this->pageSlugProvider->getPageSlug($requestDto);
                $requestDto->setPageSlug($pageSlug);

                return $requestDto;
            });
        }

        return $this->requestDto;
    }

    private function getCacheKeyFromRequest(Request $request): string
    {
        return md5($request->getLocale().$request->getHost().$request->getPathInfo());
    }
}
