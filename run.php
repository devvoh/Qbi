<?php
/*
 * In lieu of an autoloader
 */
require_once('src/Application.php');
require_once('src/Parser.php');
require_once('src/Supervisor.php');
require_once('src/Parser/Line.php');
require_once('src/Console/Output.php');
require_once('src/Console/Input.php');

$supervisor = new \Qbi\Supervisor(new \Qbi\Console\Output(), new \Qbi\Console\Input());
$parser     = new \Qbi\Parser(new \Qbi\Console\Output(), new \Qbi\Console\Input());

$qbi = new Qbi\Application($supervisor, $parser, new \Qbi\Console\Output(), new \Qbi\Console\Input());

$qbi->start();
