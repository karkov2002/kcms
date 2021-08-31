<?php

namespace Karkov\Kcms\Tests\Form\EditPage;

use Karkov\Kcms\Entity\PageContent;
use Karkov\Kcms\Form\EditPage\PageContentType;
use Karkov\Kcms\Service\Helper\DateTimer;
use Symfony\Component\Form\Test\TypeTestCase;

class PageContentTypeTest extends TypeTestCase
{
    public function testPageContentType()
    {
        $formData = [
            'rank' => 2,
            'date_start' => ['date' => '2020-01-01', 'time' => ['hour' => '0', 'minute' => '0']],
            'date_end' => ['date' => '2040-01-01', 'time' => ['hour' => '0', 'minute' => '0']],
        ];

        $form = $this->factory->create(PageContentType::class);

        $form->submit($formData);
        $result = $form->getData();

        $expected = new PageContent();
        $expected
            ->setRank(2)
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2040-01-01'))
        ;

        $this->assertEquals($expected, $result);
    }
}
