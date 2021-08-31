<?php

namespace Karkov\Kcms\Tests\Form\EditPage;

use Karkov\Kcms\Entity\PageContent;
use Karkov\Kcms\Form\EditPage\PageContentsType;
use Karkov\Kcms\Service\Helper\DateTimer;
use Symfony\Component\Form\Test\TypeTestCase;

class PageContentsTypeTest extends TypeTestCase
{
    public function testPageContentsType()
    {
        $formData =
            ['pageContents' => [
                    0 => [
                        'rank' => 1,
                        'date_start' => ['date' => '2020-01-01', 'time' => ['hour' => '0', 'minute' => '0']],
                        'date_end' => ['date' => '2040-01-01', 'time' => ['hour' => '0', 'minute' => '0']],
                    ],
                    1 => [
                        'rank' => 2,
                        'date_start' => ['date' => '2025-01-01', 'time' => ['hour' => '0', 'minute' => '0']],
                        'date_end' => ['date' => '2045-01-01', 'time' => ['hour' => '0', 'minute' => '0']],
                    ],
                ],
            ]
        ;

        $form = $this->factory->create(PageContentsType::class);

        $form->submit($formData);
        $result = $form->getData();

        $pageContent01 = new PageContent();
        $pageContent01
            ->setRank(1)
            ->setDateStart((new DateTimer())->get('2020-01-01'))
            ->setDateEnd((new DateTimer())->get('2040-01-01'))
        ;

        $pageContent02 = new PageContent();
        $pageContent02
            ->setRank(2)
            ->setDateStart((new DateTimer())->get('2025-01-01'))
            ->setDateEnd((new DateTimer())->get('2045-01-01'))
        ;

        $expected = ['pageContents' => [$pageContent01, $pageContent02]];

        $this->assertEquals($expected, $result);
    }
}
