<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Form\EditContent\ContentLocalTextType;

class TextModule extends AbstractModule
{
    public function getContent(RequestDto $requestDto)
    {
        return $this->rawContent;
    }

    public static function getFormType(): string
    {
        return ContentLocalTextType::class;
    }
}
