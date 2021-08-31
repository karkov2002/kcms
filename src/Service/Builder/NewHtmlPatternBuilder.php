<?php

namespace Karkov\Kcms\Service\Builder;

use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Entity\Tree;

class NewHtmlPatternBuilder
{
    public function build(JsTreeCreateNodeDto $dto, Tree $parentDirectory): HtmlPattern
    {
        $htmlPattern = new HtmlPattern();
        $htmlPattern
            ->setTitle($dto->label)
            ->setParent($parentDirectory)
        ;

        return $htmlPattern;
    }
}
