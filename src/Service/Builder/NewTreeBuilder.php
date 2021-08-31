<?php

namespace Karkov\Kcms\Service\Builder;

use Karkov\Kcms\Dto\JsTreeCreateNodeDto;
use Karkov\Kcms\Entity\Tree;

class NewTreeBuilder
{
    public function build(JsTreeCreateNodeDto $dto, Tree $parentDirectory): Tree
    {
        $tree = new Tree();
        $tree
            ->setType($dto->typeTree)
            ->setName($dto->label)
            ->setParent($parentDirectory)
        ;

        return $tree;
    }
}
