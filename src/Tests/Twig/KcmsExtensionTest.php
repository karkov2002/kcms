<?php

namespace Karkov\Kcms\Tests\Twig;

use Karkov\Kcms\Twig\KcmsExtension;
use PHPUnit\Framework\TestCase;

class KcmsExtensionTest extends TestCase
{
    public function testGetExtension()
    {
        $extension = new KcmsExtension();

        $this->assertEquals('removeSection', $extension->getFilters()[0]->getName());
        $this->assertEquals('removeSection', $extension->getFilters()[0]->getCallable()[1]);
    }

    public function testRemoveSection()
    {
        $extension = new KcmsExtension();

        $result = $extension->removeSection('<section class="kcms_zone kcms_zone_0" data-zone="0"><section id="79cc53f1db918d79d6940c7aae5c10020830f97d" data-content-id="7" class="kcms_content kcms_content_7">A page title
</section>
</section>');
        $this->assertEquals('A page title', $result);

        $result = $extension->removeSection('<section class="kcms_zone kcms_zone_1" data-zone="1"><section id="1f45542463eeb95c7333bc8145ce61bd3ad67516" data-content-id="87" class="kcms_content kcms_content_87"><meta http-equiv="Content-Language" content="fr-FR" /><meta name="application-name" content="Kcms" /></section></section>');
        $this->assertEquals('<meta http-equiv="Content-Language" content="fr-FR" /><meta name="application-name" content="Kcms" />', $result);
    }
}
