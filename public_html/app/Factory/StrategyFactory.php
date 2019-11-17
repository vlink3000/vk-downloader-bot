<?php

declare(strict_types=1);

namespace App\Factory;

use App\Context\StrategyContext;
use App\Strategy\GetPhotos;
use App\Strategy\GetUserPhotos;

class StrategyFactory
{
    public function chooseStrategy($request)
    {
        //set up some default strategy
        $context = new StrategyContext(new GetPhotos());

        switch ($request) {
            case '/photos':
                return $context->getResponse($request);
                break;
        }
    }
}