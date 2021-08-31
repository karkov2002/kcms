<?php

namespace Karkov\Kcms;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KcmsBundle extends Bundle
{
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getConfig()
    {
        return $this->container->getParameter('kcms');
    }
}
