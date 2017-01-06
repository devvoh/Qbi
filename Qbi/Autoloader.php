<?php
spl_autoload_register(function($classname) {
    $filename = str_replace('\\', '/', $classname);
    $path     = realpath(__DIR__ . '/..') . '/' . $filename . '.php';
    var_dump($path);
    if (file_exists($path)) {
        require_once $path;
    }
});