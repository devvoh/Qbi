<?php
/*
 * In lieu of an autoloader
 */
require_once('Qbi/Autoloader.php');

$config = new Qbi\Config();
$file   = new Qbi\File($config);

$supervisor = new \Qbi\Supervisor(
    $config,
    $file,
    new \Qbi\Console\Output(),
    new \Qbi\Console\Input()
);
$parser     = new \Qbi\Parser(
    $config,
    $file,
    new \Qbi\Console\Output(),
    new \Qbi\Console\Input()
);

$qbi = new Qbi\Application(
    $config,
    $supervisor,
    $parser,
    new \Qbi\Console\Output(),
    new \Qbi\Console\Input()
);

$qbi->start();
