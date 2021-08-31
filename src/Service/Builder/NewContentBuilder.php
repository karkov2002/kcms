<?php

namespace Karkov\Kcms\Service\Builder;

use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Entity\Tree;
use Karkov\Kcms\Modules\TextModule;

class NewContentBuilder
{
    const DEFAULT_MODULE = TextModule::class;

    public function build(JsTreeCreateNodeDto $dto, Tree $parentDirectory): Content
    {
        $content = new Content();
        $content
            ->setTitle($dto->label)
            ->setParent($parentDirectory)
            ->setModule(self::DEFAULT_MODULE)
        ;

        return $content;
    }
}
