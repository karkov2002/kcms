<?php

namespace Karkov\Kcms\Tests\Form\EditContent;

use Karkov\Kcms\Form\EditContent\ImgType;
use Symfony\Component\Form\Test\TypeTestCase;

class ImgTypeTest extends TypeTestCase
{
    public function testImgType()
    {
        $formData = ['content' => 'a content'];

        $view = $this->factory->create(ImgType::class, $formData)->createView();

        $this->assertEquals('kcms_img', $view->vars['attr']['class']);
        $this->assertEquals('img_kcms_', $view->vars['name']);

        $form = $this->factory->create(ImgType::class);
        $form->submit($formData);
        $result = $form->getData();

        $this->assertNull($result);
    }
}
