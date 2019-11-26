
<?php

spl_autoload_register(function ($className) {

    $rootDir =  __DIR__ . '/';

    $classPath = str_replace('\\', '/', str_replace('App', 'app', $className)) . '.php';

    $file = $rootDir . $classPath;

    require_once ($file);
});