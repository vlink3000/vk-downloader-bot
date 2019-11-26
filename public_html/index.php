<?php

declare(strict_types=1);

require_once('autoload.php');

use App\Factory\StrategyFactory;

$file = 'stat.txt';
$stat = file_get_contents ($file);
$newRequest = intval($stat) + 1;
file_put_contents($file, $newRequest);

$fn = fopen("stat.txt","r");
$result = fgets($fn);
fclose($fn);

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', '99999');

$request = "/photos";

//chose response strategy
$factory = new StrategyFactory();
$response = $factory->chooseStrategy($request);