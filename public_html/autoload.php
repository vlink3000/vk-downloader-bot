<?php

declare(strict_types=1);

spl_autoload_register(function ($className) {

    $classPath = str_replace('\\', '/', str_replace('App', 'app', $className)) . '.php';

    require_once ($classPath);
});