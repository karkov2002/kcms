<?php

namespace Karkov\Kcms\Service\Helper;

class DateTimer
{
    public function get($time = 'now', \DateTimeZone $timezone = null)
    {
        return new \DateTime($time, $timezone);
    }
}
