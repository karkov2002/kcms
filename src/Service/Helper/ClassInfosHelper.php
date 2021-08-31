<?php

namespace Karkov\Kcms\Service\Helper;

class ClassInfosHelper
{
    public function getClassInfosFromFile(string $file): array
    {
        $fp = fopen($file, 'r');
        $class = $namespace = $buffer = '';

        $abstract = false;
        $interface = false;

        while (!$class) {
            if (feof($fp)) {
                break;
            }

            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);
            $tokenCount = count($tokens);

            if (false === strpos($buffer, '{')) {
                continue;
            }

            for ($i = 0; $i < $tokenCount; ++$i) {
                if (T_NAMESPACE === $tokens[$i][0]) {
                    for ($j = $i + 1; $j < $tokenCount; ++$j) {
                        if (T_STRING === $tokens[$j][0]) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } elseif ('{' === $tokens[$j] || ';' === $tokens[$j]) {
                            break;
                        }
                    }
                }

                if (T_CLASS === $tokens[$i][0]) {
                    for ($j = $i + 1; $j < $tokenCount; ++$j) {
                        if ('{' === $tokens[$j]) {
                            $class = $tokens[$i + 2][1];
                        }
                    }
                }

                if (T_ABSTRACT === $tokens[$i][0]) {
                    $abstract = true;
                }

                if (T_INTERFACE === $tokens[$i][0]) {
                    $interface = true;
                }
            }
        }

        return [
            'namespace' => $namespace,
            'class' => $class,
            'interface' => $interface,
            'abstract' => $abstract,
        ];
    }
}
