<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Http\CallVkApi;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class GetPhotos implements StrategyInterface {

    public function prepareResponse(string $request)
    {
        $payload = [
            'method' => 'users.search',
            'fields' => [
                'v' => '5.89',
                'has_photo' => '1', //true
                'sex' => '1', //female
                'age_from' => '18',
                'age_to' => '35',
                'city' => '11', //volgograd
                'sort' => '0', //0 - by popularity, 1 - reg date
                'count' => '100',
            ]
        ];

        $call = new CallVkApi();
        $response = $call->sendPostRequest($payload);

        $ids = [];
        $usersArr = json_decode(json_encode($response->response->items), True);

        $userPhotos = new GetUserPhotos();
        //get users ids
        foreach ($usersArr as $user) {
            array_push($ids, $user['id']);
        }

        $i = 0;
        foreach ($ids as $id) {
            $i++;
            $payload = $userPhotos->prepareResponse((string)$id);
            $photos = $call->sendPostRequest($payload);
            $photosArr = json_decode(json_encode($photos), True);

            if (!file_exists('photos/' . $i)) {
                mkdir('photos/' . $i, 0777, true);
            }

            foreach ($photosArr['response']['items'] as $photo) {
                $useragent = "Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15";
                $ch = curl_init ("");
                curl_setopt ($ch, CURLOPT_URL, $photo['sizes'][8]['url']);
                curl_setopt ($ch, CURLOPT_USERAGENT, $useragent); // set user agent
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                $curl = curl_exec ($ch);
                curl_close($ch);
                $imgName = strtotime("now");
                file_put_contents('photos/' . $i . '/' . $imgName . '.png', $curl);
            }
        }

        unlink('photos.zip');

        function removeDirectory($path) {
            $files = glob($path . '/*');
            foreach ($files as $file) {
                is_dir($file) ? removeDirectory($file) : unlink($file);
            }
            rmdir($path);
            return;
        }

        removeDirectory('photos');

        // Get real path for our folder
        echo $rootPath = realpath('photos');

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open('photos.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE );

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        echo("<script>location.href = 'https://vk-parser.000webhostapp.com/photos.zip';</script>");

        die();

    }
}