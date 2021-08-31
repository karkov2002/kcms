<?php

namespace Karkov\Kcms\Tests\Service\Helper;

use Karkov\Kcms\Service\Helper\DateTimer;
use PHPUnit\Framework\TestCase;

class DateTimerTest extends TestCase
{
    public function testGet()
    {
        // Given
        $dateTimer = new DateTimer();

        // When
        $dateTimer = $dateTimer->get('2020-01-01');

        // Then
        $expected = new \DateTime('2020-01-01');
        $this->assertEquals($expected, $dateTimer);
    }
}
