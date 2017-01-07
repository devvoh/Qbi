<?php
spl_autoload_register(function($classname) {
    $filename = str_replace('\\', '/', $classname);
    $path     = realpath(__DIR__ . '/..') . '/' . $filename . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});