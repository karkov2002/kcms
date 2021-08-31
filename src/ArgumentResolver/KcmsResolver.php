<?php

namespace Karkov\Kcms\ArgumentResolver;

use Karkov\Kcms\Dto\KcmsDto;
use Karkov\Kcms\Service\Provider\KcmsDtoProvider;
use Karkov\Kcms\Service\Provider\RequestDtoProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class KcmsResolver implements ArgumentValueResolverInterface
{
    private $kcmsDtoProvider;
    private $requestDtoProvider;

    public function __construct(RequestDtoProvider $requestDtoProvider, KcmsDtoProvider $kcmsDtoProvider)
    {
        $this->kcmsDtoProvider = $kcmsDtoProvider;
        $this->requestDtoProvider = $requestDtoProvider;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return KcmsDto::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $requestDto = $this->requestDtoProvider->provideRequestDtoFromRequest($request);

        if (null === $requestDto) {
            yield new KcmsDto();

            return;
        }

        yield $this->kcmsDtoProvider->provideKcmsDto($requestDto);
    }
}
