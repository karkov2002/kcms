<?php

namespace Karkov\Kcms\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class TablePrefixEventListener
{
    /**
     * @var array
     */
    protected $config;

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable([
                'name' => $this->getPrefix($classMetadata->getName(), $classMetadata->getTableName()).$classMetadata->getTableName(),
            ]);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'] && $mapping['isOwningSide']) {
                $mappedTableName = $mapping['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->getPrefix($mapping['targetEntity'], $mappedTableName).$mappedTableName;
            }
        }
    }

    protected function getPrefix(string $className, string $tableName): string
    {
        $nameSpaces = explode('\\', $className);
        $bundleName = isset($nameSpaces[1]) ? strtolower($nameSpaces[1]) : null;

        if (!$bundleName || !isset($this->config[$bundleName])) {
            return '';
        }

        $prefix = $this->config[$bundleName];

        // table is already prefixed with bundle name
        if (0 === strpos($tableName, $prefix)) {
            return '';
        }

        return $prefix;
    }
}
