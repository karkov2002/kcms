<?php

namespace Karkov\Kcms\Service\Provider;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Repository\PageSlugRepository;
use Karkov\Kcms\Repository\SiteRepository;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Contracts\Cache\CacheInterface;

class PageSlugProvider
{
    private $pageSlugRepository;
    private $siteRepository;
    private $config;
    private $cache;

    public function __construct(PageSlugRepository $pageSlugRepository, SiteRepository $siteRepository, CacheInterface $cache, array $config)
    {
        $this->pageSlugRepository = $pageSlugRepository;
        $this->siteRepository = $siteRepository;
        $this->config = $config;
        $this->cache = $cache;
    }

    public function provideNewPageSlug(string $slug, ?string $local, ?Page $page): PageSlug
    {
        $slug = '/'.ltrim($slug, '/');

        $pageSlug = new PageSlug();
        $pageSlug
            ->setSlug($slug)
            ->setLocal($local)
            ->setPage($page)
        ;

        return $pageSlug;
    }

    public function getPageSlug(RequestDto $requestDto): ?PageSlug
    {
        $site = $this->siteRepository->findOneBy(['domain' => $requestDto->getHost()]);

        if (null === $site || false === $site->getIsEnable()) {
            return null;
        }

        $cacheKey = $site->getDomain().$site->getIsEnable().$requestDto->getLocal();
        $routeCollection = $this->cache->get($cacheKey, function () use ($site, $requestDto) {
            $pageSlugs = $this->pageSlugRepository->findAllSlugsBySiteAndLocal($site, $requestDto->getLocal());

            return $this->getRouteCollection($pageSlugs);
        });

        $matcher = new UrlMatcher($routeCollection, new RequestContext());

        try {
            $attributes = $matcher->matchRequest($requestDto->getRequest());
        } catch (\Exception $exception) {
            return null;
        }

        $pageSlug = $attributes['pageSlug'];
        $pageSlug->setRouteAttributes($attributes);

        return $pageSlug;
    }

    private function getRouteCollection(array $pageSlugs): RouteCollection
    {
        $routeCollection = new RouteCollection();
        foreach ($pageSlugs as $pageSlug) {
            if (true === $this->config['multilingual']['enable']) {
                $url = '{_local}'.$pageSlug->getSlug();
            } else {
                $url = $pageSlug->getSlug();
            }
            $name = $pageSlug->getSlug();
            $routeCollection->add($name, new Route($url, ['pageSlug' => $pageSlug]));
        }

        return $routeCollection;
    }
}
