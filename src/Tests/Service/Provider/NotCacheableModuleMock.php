<?php

namespace Karkov\Kcms\Tests\Service\Provider;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Modules\AbstractModule;

class NotCacheableModuleMock extends AbstractModule
{
    public function getContent(RequestDto $requestDto)
    {
        return $this->rawContent;
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
