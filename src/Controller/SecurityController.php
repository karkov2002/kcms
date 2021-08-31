<?php

namespace Karkov\Kcms\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    public const ROLES_ALLOWED = 'ROLE_ADMIN_KCMS';

    /**
     * @Route("/is_connected", name="is_connected")
     */
    public function isConnected(Request $request, Security $security)
    {
        $isConnected = false;
        $user = $security->getUser();

        if (null !== $user && in_array(self::ROLES_ALLOWED, $user->getRoles())) {
            $isConnected = true;
        }

        return new JsonResponse(['connected' => $isConnected]);
    }
}
