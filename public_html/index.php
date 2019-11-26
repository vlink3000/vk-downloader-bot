<?php

declare(strict_types=1);

require_once('autoload.php');

use App\Factory\StrategyFactory;

$request = "/photos";

//chose response strategy
$factory = new StrategyFactory();
$response = $factory->chooseStrategy($request);