<?php

namespace Karkov\Kcms\Tests\Service\ContentLocal;

use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Form\DataTransformer\ComposedTransformer;
use Karkov\Kcms\Serializer\ComposedModuleElementExtractor;
use Karkov\Kcms\Service\ContentLocal\HtmlPatternManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class HtmlPatternManagerTest extends TestCase
{
    public function testChangeHtmlPattern()
    {
        // Given
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ComposedModuleElementExtractor()),
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $composedTransformer = new ComposedTransformer($serializer);
        $htmlPatternManager = new HtmlPatternManager($composedTransformer);

        $contentLocal = new ContentLocal();

        $currentHtmlPattern = new HtmlPattern();
        $currentHtmlPattern
            ->setTitle('a pattern html')
            ->setPattern('<div class="abstract">{{ELEMENT:1:TXT_AREA}}</div>');

        $contentLocal->setHtmlPattern($currentHtmlPattern);

        // When
        $newHtmlPattern = new HtmlPattern();
        $newHtmlPattern
            ->setTitle('a new pattern html')
            ->setPattern('<div class="abstract">{{ELEMENT:1:HTML}}</div>');

        $result = $htmlPatternManager->changeHtmlPattern($contentLocal, $newHtmlPattern);

        // Then
        $this->assertEquals($newHtmlPattern, $result->getHtmlPattern());
    }
}
