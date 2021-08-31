<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;

class HelloModule extends AbstractModule
{
    public function getContent(RequestDto $requestDto)
    {
        return sprintf('Hello %s', $this->getName($requestDto));
    }

    public function getCacheKey(RequestDto $requestDto): string
    {
        return sha1(self::class.$this->getName($requestDto));
    }

    private function getName(RequestDto $requestDto): ?string
    {
        if (isset($requestDto->getPageSlug()->getRouteAttributes()['name'])) {
            $name = $requestDto->getPageSlug()->getRouteAttributes()['name'];
        } else {
            $name = $requestDto->getRequest()->query->get('name');
        }

        return $name;
    }
}
