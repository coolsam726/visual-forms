<?php

namespace Coolsam\VisualForms;

use Symfony\Component\Finder\SplFileInfo;

class Utils
{
    public static function getFileNamespace(SplFileInfo $file, $baseNamespace = 'Coolsam\\VisualForms'): string
    {
        $namespace = $baseNamespace;
        $path = $file->getRelativePath();
        if ($path) {
            $namespace .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $path);
        }
        $namespace .= '\\' . $file->getBasename('.php');

        return $namespace;
    }

    public static function instantiateClass(string $namespace, $args = [])
    {
        return new $namespace(...$args);
    }

    public static function getBool(string $boolValue): bool
    {
        return filter_var($boolValue, FILTER_VALIDATE_BOOLEAN);
    }
}
