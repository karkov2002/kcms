<?php

namespace Karkov\Kcms\Service\ContentLocal;

use Karkov\Kcms\Entity\ContentLocal;
use Karkov\Kcms\Entity\HtmlPattern;
use Karkov\Kcms\Form\DataTransformer\ComposedTransformer;

class HtmlPatternManager
{
    private $composedTransformer;

    public function __construct(ComposedTransformer $composedTransformer)
    {
        $this->composedTransformer = $composedTransformer;
    }

    public function changeHtmlPattern(ContentLocal $contentLocal, HtmlPattern $htmlPattern): ContentLocal
    {
        $composedModuleDto = $this->composedTransformer->transform($contentLocal->getRawContent());
        $composedModuleDto->setPatternHtml($htmlPattern->getPattern());

        $contentLocal->setRawContent($this->composedTransformer->reverseTransform($composedModuleDto));
        $contentLocal->setHtmlPattern($htmlPattern);

        return $contentLocal;
    }
}
