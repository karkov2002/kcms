<?php

namespace Karkov\Kcms\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class KcmsExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('removeSection', [$this, 'removeSection']),
        ];
    }

    public function removeSection($input)
    {
        $pattern = '/<section .* class="kcms_[^>]*>|<\/section>/i';

        return trim(preg_replace($pattern, '', $input));
    }
}
