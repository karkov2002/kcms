<?php

namespace Karkov\Kcms\Tests\Modules;

use Doctrine\Common\Collections\ArrayCollection;
use Karkov\Kcms\Dto\RequestDto;
use Karkov\Kcms\Entity\PageSlug;
use Karkov\Kcms\Form\EditContent\ContentLocalComposedType;
use Karkov\Kcms\Form\EditContent\ContentLocalDefaultType;
use Karkov\Kcms\Form\EditContent\ContentLocalHtmlLightType;
use Karkov\Kcms\Form\EditContent\ContentLocalHtmlType;
use Karkov\Kcms\Form\EditContent\ContentLocalTextAreaType;
use Karkov\Kcms\Form\EditContent\ContentLocalTextType;
use Karkov\Kcms\Modules\ComposedModule;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleDto;
use Karkov\Kcms\Modules\ComposedModule\ComposedModuleElement;
use Karkov\Kcms\Modules\ControllerModule;
use Karkov\Kcms\Modules\HelloModule;
use Karkov\Kcms\Modules\HtmlLightModule;
use Karkov\Kcms\Modules\HtmlModule;
use Karkov\Kcms\Modules\TextAreaModule;
use Karkov\Kcms\Modules\TextModule;
use Karkov\Kcms\Serializer\ComposedModuleElementExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ModuleTest extends TestCase
{
    public function testTextModule()
    {
        // Given
        $module = new TextModule();

        // When
        $module->setRawContent('a content');

        // Then
        $dto = new RequestDto();
        $this->assertEquals('a content', $module->getContent($dto));
        $this->assertEquals(ContentLocalTextType::class, TextModule::getFormType());
    }

    public function testTextAreaModule()
    {
        // Given
        $module = new TextAreaModule();

        // When
        $module->setRawContent('a content');

        // Then
        $dto = new RequestDto();
        $this->assertEquals('a content', $module->getContent($dto));
        $this->assertEquals(ContentLocalTextAreaType::class, TextAreaModule::getFormType());
        $this->assertEquals(true, $module->isCacheable());
        $this->assertEquals('112c74d3c737c3d2facb0b8da36e1ffab710aea1', $module->getCacheKey($dto));
    }

    public function testHtmlModule()
    {
        // Given
        $module = new HtmlModule();

        // When
        $module->setRawContent('<p>a content</p>');

        // Then
        $dto = new RequestDto();
        $this->assertEquals('<p>a content</p>', $module->getContent($dto));
        $this->assertEquals(ContentLocalHtmlType::class, HtmlModule::getFormType());
        $this->assertEquals(true, $module->isCacheable());
        $this->assertEquals('7c67465bcd2b52abd17f96e8ee0c64e8b5d01193', $module->getCacheKey($dto));
    }

    public function testHelloModule()
    {
        // Given
        $module = new HelloModule();
        $request = new Request(['name' => 'John']);
        $pageSlug = new PageSlug();
        $pageSlug->setRouteAttributes([]);

        $dto = new RequestDto();
        $dto
            ->setRequest($request)
            ->setPageSlug($pageSlug)
        ;

        // When
        $result = $module->getContent($dto);

        // Then
        $this->assertEquals('Hello John', $result);
        $this->assertEquals(ContentLocalDefaultType::class, HelloModule::getFormType());

        // Given
        $pageSlug->setRouteAttributes(['name' => 'Doo']);
        $dto->setPageSlug($pageSlug);

        // When
        $result = $module->getContent($dto);

        // Then
        $this->assertEquals('Hello Doo', $result);
        $this->assertEquals(true, $module->isCacheable());
        $this->assertEquals('bbc559a3c55466531b0df84e9b26df4e78ebb21c', $module->getCacheKey($dto));
    }

    public function testHtmlLightModule()
    {
        // Given
        $module = new HtmlLightModule();

        // When
        $module->setRawContent('<p>a content</p>');

        // Then
        $dto = new RequestDto();
        $this->assertEquals('<p>a content</p>', $module->getContent($dto));
        $this->assertEquals(ContentLocalHtmlLightType::class, HtmlLightModule::getFormType());
        $this->assertEquals(true, $module->isCacheable());
        $this->assertEquals('7c67465bcd2b52abd17f96e8ee0c64e8b5d01193', $module->getCacheKey($dto));
    }

    public function testControllerModule()
    {
        // Given
        $response = new Response();
        $response->setContent('<body>Html response</body>');
        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $httpKernel->method('handle')->willReturn($response);
        $requestStackMock = $this->createMock(RequestStack::class);
        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method('get')->willReturn($httpKernel);
        $module = new ControllerModule($requestStackMock, $containerMock);

        // When
        $module->setRawContent('App\Controller\ContentController::returnForm');

        // Then
        $dto = new RequestDto();
        $this->assertEquals('<body>Html response</body>', $module->getContent($dto));
        $this->assertEquals(ContentLocalTextType::class, ControllerModule::getFormType());
        $this->assertEquals(true, $module->isCacheable());
        $this->assertEquals('dd6136b9ba64693a04648ae1de820726cb30e58d', $module->getCacheKey($dto));
    }

    public function testComposedModule()
    {
        // Given
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ComposedModuleElementExtractor()),
        ];
        $serializer = new Serializer($normalizers, $encoders);

        $module = new ComposedModule($serializer);
        $module->setRawContent('{"patternHtml":"<p>{{ELEMENT:1:HTML}}<\/p><p>{{ELEMENT:2:IMG}}<\/p><p>{{ELEMENT:3:TEXT}}<\/p>","elements":[{"id":1,"type":"HTML","content":"<strong>Hello in html</strong>"},{"id":2,"type":"IMG","content":"\/userfiles\/images\/saturne.jpg"},{"id":3,"type":"TEXT","content":"a simple text"}],"elementsFromPattern":{"elements":["{{ELEMENT:1:HTML}}","{{ELEMENT:2:IMG}}","{{ELEMENT:3:TEXT}}"],"ids":["1","2","3"],"types":["HTML","IMG","TEXT"]}}');

        // When
        $dto = new RequestDto();
        $content = $module->getContent($dto);

        $this->assertEquals('<p><strong>Hello in html</strong></p><p><img src="/userfiles/images/saturne.jpg" /></p><p>a simple text</p>', $content);
        $this->assertEquals(ContentLocalComposedType::class, ComposedModule::getFormType());
    }

    public function testComposedModuleDto()
    {
        $element01 = new ComposedModuleElement();
        $element01
            ->setType('HTML')
            ->setContent('<p>a content html</p>')
        ;

        $element02 = new ComposedModuleElement();
        $element02
            ->setType('TXT')
            ->setContent('simple text')
        ;

        $composedModuleDto = new ComposedModuleDto();
        $composedModuleDto
            ->setPatternHtml('<p>{{ELEMENT:1:HTML}}</p><div>{{ELEMENT:2:TXT}}</div>')
            ->addElements([$element01, $element02])
        ;

        $expected = [
            'elements' => [
                0 => '{{ELEMENT:1:HTML}}',
                1 => '{{ELEMENT:2:TXT}}',
            ],
            'ids' => [
                0 => '1',
                1 => '2',
            ],
            'types' => [
                0 => 'HTML',
                1 => 'TXT',
            ],
        ];

        $this->assertEquals($expected, $composedModuleDto->getElementsFromPattern());

        $composedModuleDto->removeElements([$element01]);

        $expected = new ArrayCollection();
        $expected->add($element01);
        $expected->add($element02);
        $expected->removeElement($element01);

        $this->assertEquals($expected, $composedModuleDto->getElements());
    }
}
