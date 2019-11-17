<?php

declare(strict_types=1);

namespace App\Strategy;

class GetUserPhotos implements StrategyInterface {

    public function prepareResponse(string $userId)
    {
        return [
            'method' => 'photos.getAll',
            'fields' => [
                'v' => '5.103',
                'owner_id' => $userId,
            ]
        ];
    }
}