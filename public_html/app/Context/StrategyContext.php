<?php

declare(strict_types=1);

namespace App\Context;

use App\Strategy\StrategyInterface;

class StrategyContext
{
    private $strategy;
    /**
     * StrategyContext constructor.
     * @param StrategyInterface $strategy
     */
    public function __construct(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }
    /**
     * @param StrategyInterface $strategy
     */
    public function setStrategy(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }
    public function getResponse(string $request): array
    {
        return $this->strategy->prepareResponse($request);
    }
}