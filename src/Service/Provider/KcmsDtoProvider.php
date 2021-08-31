<?php

namespace Karkov\Kcms\Service\Provider;

use Karkov\Kcms\Dto\KcmsDto;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Exception\ModuleCannotBeAutowiredException;
use Karkov\Kcms\Exception\ModuleNotFoundException;
use Karkov\Kcms\Modules\KcmsModuleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

class KcmsDtoProvider
{
    private $config;
    private $cache;
    private $container;
    private $twig;
    private $security;

    public function __construct(
        CacheInterface $cache,
        ContainerInterface $container,
        Environment $twig,
        Security $security,
        array $config
    ) {
        $this->config = $config;
        $this->cache = $cache;
        $this->container = $container;
        $this->twig = $twig;
        $this->security = $security;
    }

    public function provideKcmsDto(RequestDto $requestDto): KcmsDto
    {
        $page = $this->getPage($requestDto);
        $zones = [];

        $kcmsDto = (new KcmsDto())
            ->setRequestDto($requestDto)
            ->setPage($page)
        ;

        for ($zoneNumber = 0; $zoneNumber < $this->config['zones']['nb']; ++$zoneNumber) {
            $zoneContent = '';

            if (null !== $page) {
                $pageContents = $page->getValidPageContentsPerZone($zoneNumber);
                foreach ($pageContents as $pageContent) {
                    $zoneContent .= $this->getRenderedContent($pageContent->getContent(), $requestDto);
                }
            }

            $zones[$zoneNumber] = $this->twig->render('@Kcms/default/elements/zone.html.twig', [
                'id' => $zoneNumber,
                'zoneContent' => $zoneContent,
            ]);
        }

        $kcmsDto->setZones($zones);

        if (null !== $this->security->getUser() && in_array('ROLE_ADMIN_KCMS', $this->security->getUser()->getRoles()) && null !== $page) {
            $kcmsDto->setJs($this->twig->render('@Kcms/default/elements/admin_js.html.twig', ['page' => $page]));
        }

        return $kcmsDto;
    }

    private function getPage(RequestDto $requestDto): ?Page
    {
        if (null === $requestDto->getPageSlug()) {
            return null;
        }

        return $requestDto->getPageSlug()->getPage();
    }

    private function getRenderedContent(Content $content, RequestDto $requestDto): string
    {
        if (null === $content) {
            return '';
        }

        $localizedContent = $content->getContentLocalsByLocal($requestDto->getLocal())->first();

        if (empty($localizedContent)) {
            return '';
        }

        $moduleName = $content->getModule();
        if (!class_exists($moduleName)) {
            throw new ModuleNotFoundException(sprintf('module %s not found', $moduleName));
        }

        $module = $this->getModule($moduleName);
        $module->setRawContent($localizedContent->getRawContent());

        if ($module->isCacheable() && $requestDto->getRequest()->isMethodSafe()) {
            $renderedContent = $this->cache->get($module->getCacheKey($requestDto),
                function () use ($module, $requestDto, $content) {
                    return $this->renderView($module, $requestDto, $content->getId());
                });
        } else {
            $renderedContent = $this->renderView($module, $requestDto, $content->getId());
        }

        $content->setRenderedContent($renderedContent);

        return $renderedContent;
    }

    private function getModule(string $moduleName): KcmsModuleInterface
    {
        try {
            $module = $this->container->get($moduleName);
        } catch (ServiceNotFoundException $exception) {
            try {
                $module = new $moduleName();
            } catch (\ArgumentCountError $exception) {
                throw new ModuleCannotBeAutowiredException(sprintf('The constructor of the module %s have some arguments, but it cannot be autowired because it is not present on the service container. You should explicitly set this module as a public service', $moduleName));
            }
        }

        return $module;
    }

    private function renderView(KcmsModuleInterface $module, RequestDto $requestDto, ?int $key): string
    {
        return $this->twig->render('@Kcms/default/elements/content.html.twig', [
            'id' => $module->getCacheKey($requestDto),
            'key' => $key,
            'content' => $module->getContent($requestDto),
        ]);
    }
}
