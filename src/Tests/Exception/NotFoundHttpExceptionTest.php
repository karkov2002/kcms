<?php

namespace Karkov\Kcms\Tests\Exception;

use Karkov\Kcms\Exception\NotFoundHttpException;
use PHPUnit\Framework\TestCase;

class NotFoundHttpExceptionTest extends TestCase
{
    public function testNotFoundException()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Kcms page not found');

        throw new NotFoundHttpException('Kcms page not found');
    }
}
