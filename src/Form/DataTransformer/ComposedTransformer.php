<?php

namespace Karkov\Kcms\Form\DataTransformer;

use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ComposedTransformer implements DataTransformerInterface
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function transform($content)
    {
        try {
            $dto = $this->serializer->deserialize($content, ComposedModuleDto::class, 'json');
        } catch (\Exception | \Error $e) {
            $dto = new ComposedModuleDto();
        }

        return $dto;
    }

    public function reverseTransform($dto)
    {
        return $this->serializer->serialize($dto, 'json');
    }
}
