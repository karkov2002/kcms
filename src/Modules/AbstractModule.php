<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Form\EditContent\ContentLocalDefaultType;

abstract class AbstractModule implements KcmsModuleInterface
{
    protected $rawContent;

    public function setRawContent(?string $rawContent)
    {
        $this->rawContent = $rawContent;
    }

    public function isCacheable(): bool
    {
        return true;
    }

    public function getCacheKey(RequestDto $requestDto): string
    {
        return sha1($this->rawContent);
    }

    public static function getFormType(): string
    {
        return ContentLocalDefaultType::class;
    }
}
