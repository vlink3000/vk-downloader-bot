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
                'count' => '5',
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

            if (isset($userPhotosArr['response']['items']) && !is_null($userPhotosArr['response']['items'])) {
                $finalPhotosArr = [];
                foreach ($userPhotosArr['response']['items'] as $key => $photos) {
                    if (isset($photos['sizes']['6'])) {
                        $finalPhotosArr[] = $photos['sizes'][6]['url'];
                    }
                }
                //add array of photos to main array
                array_push($photosArrsArr, $finalPhotosArr);
            }
        }

        $folder = 0;

        foreach ($photosArrsArr as $item) {

            //create dir for each photos set
            mkdir("photos/".$folder);
            foreach ($item as $url) {
//                $image = file_get_contents($url);
//
//                $fileName = strtotime("now");
//                file_put_contents('photos/' . $folder . '/' . $fileName . '.jpg', $image);
                echo json_encode($url);
                echo "\n";
            }

            $folder++;
        }
        die();
    }
}