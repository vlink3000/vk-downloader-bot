<?php

declare(strict_types=1);

namespace App\Strategy;

class GetCities implements StrategyInterface {

    public function prepareResponse(string $request)
    {
        return [
            'method' => 'database.getCities',
            'fields' => [
                'v' => '5.89',
                'offset' => '2', //without moscow and st. petersburg
                'country_id' => '1', //Russia
            ]
        ];
    }
}