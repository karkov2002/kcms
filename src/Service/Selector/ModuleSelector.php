<?php

namespace Karkov\Kcms\Service\Selector;

use Karkov\Kcms\Service\Helper\ClassInfosHelper;
use Symfony\Component\Finder\Finder;

class ModuleSelector
{
    const MODULE_INTERFACE = 'Karkov\Kcms\Modules\KcmsModuleInterface';
    const MODULE_FILE_PATTERN = '*Module.php';

    private $projectDir;
    private $classInfosHelper;

    public function __construct(string $projectDir, ClassInfosHelper $classInfosHelper)
    {
        $this->projectDir = $projectDir;
        $this->classInfosHelper = $classInfosHelper;
    }

    public function getList(): array
    {
        $list = [];
        $dir = [__DIR__.'/../../Modules', $this->projectDir.'/src/'];

        $finder = new Finder();
        $finder->files()->in($dir)->name(self::MODULE_FILE_PATTERN);

        foreach ($finder as $file) {
            $infos = $this->classInfosHelper->getClassInfosFromFile($file->getRealPath());
            $class = ltrim($infos['namespace'], '\\').'\\'.$infos['class'];
            try {
                $interfaces = class_implements($class);
                if (in_array(self::MODULE_INTERFACE, $interfaces) && false === $infos['abstract']) {
                    $list[$class] = $class;
                }
            } catch (\Exception $exception) {
            }
        }

        return $list;
    }
}
