<?php

declare(strict_types=1);

namespace App\Strategy;

interface StrategyInterface
{
    public function prepareResponse(string $request);
}