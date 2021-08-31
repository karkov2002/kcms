<?php

namespace Karkov\Kcms\Tests\Form\EditContent;

use Karkov\Kcms\Entity\Content;
use Karkov\Kcms\Form\EditContent\ContentType;
use Karkov\Kcms\Modules\TextAreaModule;
use Karkov\Kcms\Service\Selector\ModuleSelector;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class ContentTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $moduleSelectorMock = $this->createMock(ModuleSelector::class);
        $moduleSelectorMock->method('getList')->willReturn(['Karkov\Kcms\Modules\TextAreaModule' => 'Karkov\Kcms\Modules\TextAreaModule']);
        $type = new ContentType($moduleSelectorMock);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    public function testContentType()
    {
        $postVars = [
            'title' => 'a content',
            'module' => TextAreaModule::class,
        ];

        $form = $this->factory->create(ContentType::class);
        $form->submit($postVars);

        $result = $form->getData();

        $expected = new Content();
        $expected
            ->setTitle('a content')
            ->setModule(TextAreaModule::class)
        ;

        $this->assertEquals($expected, $result);
    }
}
