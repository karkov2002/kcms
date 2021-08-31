<?php

namespace Karkov\Kcms\Serializer;

use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleElement;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

class ComposedModuleElementExtractor implements PropertyTypeExtractorInterface
{
    private $reflectionExtractor;

    public function __construct()
    {
        $this->reflectionExtractor = new ReflectionExtractor();
    }

    /**
     * @return array|Type[]|null
     */
    public function getTypes(string $class, string $property, array $context = [])
    {
        if (\is_a($class, ComposedModuleDto::class, true) && 'elements' === $property) {
            return [
                new Type(
                    Type::BUILTIN_TYPE_ARRAY,
                    true,
                    null,
                    true,
                    new Type(Type::BUILTIN_TYPE_INT, true),
                    new Type(Type::BUILTIN_TYPE_OBJECT, true, ComposedModuleElement::class)
                ),
            ];
        }

        return $this->reflectionExtractor->getTypes($class, $property, $context);
    }
}
