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
        //prepare payload
        $payload = [
            'method' => 'users.search',
            'fields' => [
                'v' => '5.89',
                'has_photo' => '1', //true
                'sex' => '1', //female
                'age_from' => '18',
                'age_to' => '35',
                'city' => '99', //volgograd
                'sort' => '0', //0 - by popularity, 1 - reg date
                'count' => '100',
            ]
        ];
        
        //send curl request and get list of users
        $call = new CallVkApi();
        $response = $call->sendPostRequest($payload);

        //users with fields
        if(isset($response) && !is_null($response)) {
            $usersArr = json_decode(json_encode($response->response->items), True);
        } else {
            $response = $call->sendPostRequest($payload);
            $usersArr = json_decode(json_encode($response->response->items), True);
        }

        $idsArr = [];
        //get only id field from each user in foreach
        foreach ($usersArr as $key => $user) {
                $idsArr[] = $user['id'];
        }

        //get all user photos for each user
        $userPhotos = new GetUserPhotos();

        //photos folder name
        $i = 0;

        //clear urls tmp list
//        unlink('tmp.txt');

        //array of final photos in height quality
        $photosArrsArr = [];

        foreach ($idsArr as $key => $id) {
            //prepare payload for each user
            $payload = $userPhotos->prepareResponse((string)$id);
            //get all user photos
            $userPhotosArr = json_decode(json_encode($call->sendPostRequest($payload)), True);

            if(isset($userPhotosArr['response']['items']) && !is_null($userPhotosArr['response']['items'])) {
                $finalPhotosArr = [];
                foreach ($userPhotosArr['response']['items'] as $key => $photos) {
                    if (isset($photos['sizes']['6'])) {
                        $finalPhotosArr[] = $photos['sizes'][6]['url'];
                        $data = $photos['sizes'][6]['url'] . PHP_EOL;
                        $fp = fopen('tmp.txt', 'a');
                        fwrite($fp, $data);
                    } else if (isset($photos['sizes']['4'])) {
                        $finalPhotosArr[] = $photos['sizes'][4]['url'];
                        $data = $photos['sizes'][4]['url'] . PHP_EOL;
                        $fp = fopen('tmp.txt', 'a');
                        fwrite($fp, $data);
                    } else if (isset($photos['sizes']['3'])) {
                        $finalPhotosArr[] = $photos['sizes'][3]['url'];
                        $data = $photos['sizes'][3]['url'] . PHP_EOL;
                        $fp = fopen('tmp.txt', 'a');
                        fwrite($fp, $data);
                    }
                }
            }
            //add array of photos to main array
            array_push($photosArrsArr, $finalPhotosArr);
        }

        print(json_encode($photosArrsArr));die();


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