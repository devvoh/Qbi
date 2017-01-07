<?php
require_once('Qbi/Autoloader.php');

$qbi = \Qbi\DI::get(\Qbi\Application::class);
$qbi->start();
