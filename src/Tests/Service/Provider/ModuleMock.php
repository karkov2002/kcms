<?php

namespace Karkov\Kcms\Tests\Service\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Modules\AbstractModule;

class ModuleMock extends AbstractModule
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getContent(RequestDto $requestDto)
    {
        return '';
    }
}
