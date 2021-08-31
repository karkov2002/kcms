<?php

namespace Karkov\Kcms\Controller;

use Karkov\Kcms\Exception\NotFoundHttpException;
use Karkov\Kcms\Service\Provider\KcmsDtoProvider;
use Karkov\Kcms\Service\Provider\RequestDtoProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class KcmsApiController extends AbstractController
{
    private $requestDtoProvider;
    private $kcmsDtoProvider;

    public function __construct(RequestDtoProvider $requestDtoProvider, KcmsDtoProvider $kcmsDtoProvider)
    {
        $this->requestDtoProvider = $requestDtoProvider;
        $this->kcmsDtoProvider = $kcmsDtoProvider;
    }

    /**
     * @Route("/api/{slug}", requirements={"slug":".+"}, name="kcms_api")
     */
    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $requestApi = $request->duplicate(null, null, null, null, null, ['REQUEST_URI' => $slug, null]);
        $requestApi->headers->set('HOST', $request->getHost());

        $requestDto = $this->requestDtoProvider->provideRequestDtoFromRequest($requestApi);

        if (null === $requestDto || null === $requestDto->getPageSlug()) {
            throw new NotFoundHttpException('Kcms page not found');
        }

        $kcmsDto = $this->kcmsDtoProvider->provideKcmsDto($requestDto);

        return new JsonResponse($kcmsDto->getZones());
    }
}
