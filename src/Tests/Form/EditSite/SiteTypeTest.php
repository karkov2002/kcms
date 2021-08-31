<?php

namespace Karkov\Kcms\Tests\Form\EditSite;

use Karkov\Kcms\Entity\Site;
use Karkov\Kcms\Form\EditSite\SiteType;
use Symfony\Component\Form\Test\TypeTestCase;

class SiteTypeTest extends TypeTestCase
{
    public function testSiteType()
    {
        $formData = [
            'domain' => 'domain.net',
            'isEnable' => true,
        ];

        $form = $this->factory->create(SiteType::class);

        $form->submit($formData);
        $result = $form->getData();

        $expectedSite = new Site();
        $expectedSite
            ->setDomain('domain.net')
            ->setIsEnable(true)
        ;
        $this->assertEquals($expectedSite, $result);
    }
}
