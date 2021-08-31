<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Form\EditContent\ContentLocalHtmlType;

class HtmlModule extends AbstractModule
{
    public function getContent(RequestDto $requestDto)
    {
        return $this->rawContent;
    }

    public static function getFormType(): string
    {
        return ContentLocalHtmlType::class;
    }
}
