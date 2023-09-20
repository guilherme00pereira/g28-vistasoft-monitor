<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitea944cabdc7551273f4551aba65af29a
{
    public static $files = array (
        '869ee31a091601c24a3f9acc4fb36871' => __DIR__ . '/../..' . '/src/startup.php',
    );

    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'G28\\VistasoftMonitor\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'G28\\VistasoftMonitor\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitea944cabdc7551273f4551aba65af29a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitea944cabdc7551273f4551aba65af29a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitea944cabdc7551273f4551aba65af29a::$classMap;

        }, null, ClassLoader::class);
    }
}
