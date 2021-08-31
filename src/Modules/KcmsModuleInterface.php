<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;

interface KcmsModuleInterface
{
    public function setRawContent(?string $rawContent);

    public function getContent(RequestDto $requestDto);

    public function isCacheable(): bool;

    public function getCacheKey(RequestDto $requestDto): string;

    public static function getFormType(): string;
}
