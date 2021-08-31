<?php

namespace Karkov\Kcms\Service\Builder;

use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\Entity\Tree;

class NewPageBuilder
{
    const DEFAULT_TEMPLATE = '@KcmsBundle/default/kcms.default.html.twig';

    public function build(JsTreeCreateNodeDto $dto, Tree $parentDirectory): Page
    {
        $page = new Page();
        $page
            ->setTitle($dto->label)
            ->setTemplate(self::DEFAULT_TEMPLATE)
            ->setParent($parentDirectory)
        ;

        return $page;
    }
}
