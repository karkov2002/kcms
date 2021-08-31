<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Form\EditContent\ContentLocalHtmlLightType;

class HtmlLightModule extends AbstractModule
{
    public function getContent(RequestDto $requestDto)
    {
        return $this->rawContent;
    }

    public static function getFormType(): string
    {
        return ContentLocalHtmlLightType::class;
    }
}
