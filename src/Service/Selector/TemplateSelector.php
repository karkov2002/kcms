<?php

namespace Karkov\Kcms\Service\Selector;

use Symfony\Component\Finder\Finder;

class TemplateSelector
{
    const TEMPLATE_FILE_PATTERN = 'kcms.*.html.twig';

    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getList(): array
    {
        $dirs = [
            '@KcmsBundle/' => __DIR__.'/../../Resources/views',
            '@App/' => $this->projectDir.'/templates/',
        ];

        $list = [];

        foreach ($dirs as $namespace => $dir) {
            $finder = new Finder();
            try {
                $finder->files()->in($dir)->name(self::TEMPLATE_FILE_PATTERN);
                foreach ($finder as $file) {
                    $list[$namespace.$file->getRelativePathname()] = $namespace.$file->getRelativePathname();
                }
            } catch (\Exception $exception) {
            }
        }

        return $list;
    }
}
