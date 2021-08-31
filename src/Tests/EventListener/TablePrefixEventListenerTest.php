<?php

namespace Karkov\Kcms\Tests\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Karkov\Kcms\Entity\Page;
use Karkov\Kcms\EventListener\TablePrefixEventListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TablePrefixEventListenerTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testSetConfig()
    {
        $tablePrefix = new TablePrefixEventListener();
        $tablePrefix->setConfig(['kcms' => 'kcms_']);

        $this->assertEquals(['kcms' => 'kcms_'], $tablePrefix->getConfig());
    }

    public function testLoadClassMetadata()
    {
        $classMetaData = $this->entityManager->getClassMetadata(Page::class);

        $loadClassMetadataEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $loadClassMetadataEventArgs->method('getClassMetadata')->willReturn($classMetaData);

        $tablePrefix = new TablePrefixEventListener();
        $tablePrefix->setConfig(['kcms' => 'kcms_']);

        $tablePrefix->loadClassMetadata($loadClassMetadataEventArgs);
        $this->assertEquals(['name' => 'kcms_page'], $classMetaData->table);
    }

    public function testLoadClassMetadataWithNoConfig()
    {
        $classMetaData = $this->entityManager->getClassMetadata(Page::class);

        $loadClassMetadataEventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $loadClassMetadataEventArgs->method('getClassMetadata')->willReturn($classMetaData);

        $tablePrefix = new TablePrefixEventListener();
        $tablePrefix->setConfig([]);

        $tablePrefix->loadClassMetadata($loadClassMetadataEventArgs);
        $this->assertEquals(['name' => 'kcms_page'], $classMetaData->table);
    }
}
