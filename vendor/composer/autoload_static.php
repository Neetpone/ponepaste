<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5bf95489f4eff2c10ec062bf7ba377da
{
    public static $files = array (
        'b6ec61354e97f32c0ae683041c78392a' => __DIR__ . '/..' . '/scrivo/highlight.php/HighlightUtilities/functions.php',
    );

    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'Highlight\\' => 
            array (
                0 => __DIR__ . '/..' . '/scrivo/highlight.php',
            ),
            'HighlightUtilities\\' => 
            array (
                0 => __DIR__ . '/..' . '/scrivo/highlight.php',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit5bf95489f4eff2c10ec062bf7ba377da::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit5bf95489f4eff2c10ec062bf7ba377da::$classMap;

        }, null, ClassLoader::class);
    }
}
