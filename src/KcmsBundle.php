<?php
/**
 * Kcms Bundle
 */

namespace Karkov\Kcms;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
