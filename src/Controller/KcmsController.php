<?php

// src/Controller/Kcms.php using bundle

namespace Karkov\Kcms\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class KcmsController extends Controller
{
	/**
	 * @Route("kcms")
	 */
	public function buildPage()
	{
		return new Response(
            '<html><body>Hello from kcms bundle</body></html>'
        );
	}

}
