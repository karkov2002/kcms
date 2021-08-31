<?php

namespace Karkov\Kcms\Tests\Form\EditContent;

use Karkov\Kcms\Form\DataTransformer\ComposedTransformer;
use Karkov\Kcms\Form\EditContent\ComposedType;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleElement;
use Karkov\Kcms\Serializer\ComposedModuleElementExtractor;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ComposedTypeTest extends TypeTestCase
{
    private $composedTransformer;

    protected function setUp(): void
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ComposedModuleElementExtractor()),
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $this->composedTransformer = new ComposedTransformer($serializer);

        parent::setUp();
    }

    protected function getExtensions()
    {
        $type = new ComposedType($this->composedTransformer);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    public function testComposedType()
    {
        $postVars = [
            'patternHtml' => '<p>{{ELEMENT:1:HTML}}{{ELEMENT:2:IMG}}</p>',
            'elements' => [],
        ];

        $form = $this->factory->create(ComposedType::class);
        $form->submit($postVars);

        $result = $form->getData();

        $element01 = new ComposedModuleElement();
        $element01
            ->setId(1)
            ->setContent('')
            ->setType('HTML')
        ;

        $element02 = new ComposedModuleElement();
        $element02
            ->setId(2)
            ->setContent('')
            ->setType('IMG')
        ;

        $expectedDto = new ComposedModuleDto();
        $expectedDto
            ->setPatternHtml('<p>{{ELEMENT:1:HTML}}{{ELEMENT:2:IMG}}</p>')
            ->addElements([$element01, $element02])
        ;

        $this->assertEquals($expectedDto, $result);
    }
}
