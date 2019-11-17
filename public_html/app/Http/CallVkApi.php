<?php

declare(strict_types=1);

namespace App\Http;

class CallVkApi
{
    public function sendPostRequest(array $params)
    {
        $config = include ($_SERVER["DOCUMENT_ROOT"] . '/app/Config/config.php');

        $method = $params['method'];
        $fields = $params['fields'];

        $baseUrl = $config['url'];
        $token = $config['token'];

        if(!empty($params)) {
            $url = $baseUrl . $method . '?' . http_build_query($fields) . '&access_token=' . $token;
        } else {
            $url = $baseUrl . $method;
        }

        $response = file_get_contents($url);
        $responseArr = json_decode($response);

        return $responseArr;
    }
}