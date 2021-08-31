<?php

namespace Karkov\Kcms\Tests\Form\DataTransformer;

use Karkov\Kcms\Form\DataTransformer\ComposedTransformer;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleElement;
use Karkov\Kcms\Serializer\ComposedModuleElementExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ComposedTransformerTest extends TestCase
{
    public function testTransform()
    {
        // Given
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ComposedModuleElementExtractor()),
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $composedTransformer = new ComposedTransformer($serializer);

        // When
        $content = '{"patternHtml":"<p>{{ELEMENT:1:HTMLLIGHT}}<\/p>","elements":[{"id":1,"type":"HTMLLIGHT","content":"<p>hello</p>"}],"elementsFromPattern":{"elements":["{{ELEMENT:1:HTMLLIGHT}}"],"ids":["1"],"types":["HTMLLIGHT"]}}';
        $result = $composedTransformer->transform($content);

        // Then
        $expected = new ComposedModuleDto();
        $expected->setPatternHtml('<p>{{ELEMENT:1:HTMLLIGHT}}</p>');
        $element = new ComposedModuleElement();
        $element
            ->setContent('<p>hello</p>')
            ->setType('HTMLLIGHT')
            ->setId(1)
        ;
        $expected->addElements([$element]);

        $this->assertEquals($expected, $result);
    }

    public function testExceptionWhenTransform()
    {
        // Given
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ComposedModuleElementExtractor()),
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $composedTransformer = new ComposedTransformer($serializer);

        // When
        $content = 'not_a_json_content';
        $result = $composedTransformer->transform($content);

        // Then
        $expected = new ComposedModuleDto();
        $this->assertEquals($expected, $result);
    }

    public function testReverseTransform()
    {
        // Given
        $composedModuleDto = new ComposedModuleDto();
        $composedModuleDto->setPatternHtml('<p>{{ELEMENT:1:HTMLLIGHT}}</p>');
        $element = new ComposedModuleElement();
        $element
            ->setContent('<p>hello</p>')
            ->setType('HTMLLIGHT')
            ->setId(1)
        ;
        $composedModuleDto->addElements([$element]);

        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ComposedModuleElementExtractor()),
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $composedTransformer = new ComposedTransformer($serializer);

        // When
        $json = $composedTransformer->reverseTransform($composedModuleDto);

        // Then
        $expected = '{"patternHtml":"<p>{{ELEMENT:1:HTMLLIGHT}}<\/p>","elements":[{"id":1,"type":"HTMLLIGHT","content":"<p>hello<\/p>"}],"elementsFromPattern":{"elements":["{{ELEMENT:1:HTMLLIGHT}}"],"ids":["1"],"types":["HTMLLIGHT"]}}';
        $this->assertEquals($expected, $json);
    }
}
