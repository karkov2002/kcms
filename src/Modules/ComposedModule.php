<?php

namespace Karkov\Kcms\Modules;

use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Form\EditContent\ContentLocalComposedType;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Symfony\Component\Serializer\SerializerInterface;

class ComposedModule extends AbstractModule
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getContent(RequestDto $requestDto)
    {
        $composedModuleDto = $this->getComposedModuleDto();

        return $this->recomposeContent($composedModuleDto);
    }

    public static function getFormType(): string
    {
        return ContentLocalComposedType::class;
    }

    private function getComposedModuleDto(): ComposedModuleDto
    {
        return $this->serializer->deserialize($this->rawContent, ComposedModuleDto::class, 'json');
    }

    private function recomposeContent(ComposedModuleDto $composedModuleDto): string
    {
        $html = $composedModuleDto->getPatternHtml();

        foreach ($composedModuleDto->getElements() as $element) {
            $search = '{{ELEMENT:'.$element->getId().':'.$element->getType().'}}';

            switch ($element->getType()) {
                case 'IMG':
                    $replace = sprintf('<img src="%s" />', $element->getContent());
                    break;
                case 'TEXT':
                    $replace = nl2br(strip_tags($element->getContent()));
                    break;
                default:
                    $replace = $element->getContent();
            }

            $html = str_replace($search, $replace, $html);
        }

        return $html;
    }
}
