<?php

namespace Karkov\Kcms\Tests\Form\EditPage;

use Karkov\Kcms\Form\EditPage\AddSlugType;
use Karkov\Kcms\Service\Selector\LocalSelector;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class AddSlugTypeTest extends TypeTestCase
{
    private $localSelectorMock;

    protected function setUp(): void
    {
        $this->localSelectorMock = $this->createMock(LocalSelector::class);
        $this->localSelectorMock->method('getList')->willReturn(['fr_FR' => 'fr_FR']);

        parent::setUp();
    }

    protected function getExtensions()
    {
        $type = new AddSlugType($this->localSelectorMock);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    public function testAddSlugType()
    {
        $formData = [
            'local' => 'fr_FR',
            'slug' => '/a-slug',
        ];

        $form = $this->factory->create(AddSlugType::class);

        $form->submit($formData);
        $result = $form->getData();

        $this->assertEquals(['local' => 'fr_FR', 'slug' => '/a-slug'], $result);
    }
}
