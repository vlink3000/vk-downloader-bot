<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Http\CallVkApi;

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
                'city' => '1', //volgograd
                'sort' => '0', //0 - by popularity, 1 - reg date
                'count' => '300',
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

        print_r(json_encode($photosArrsArr));die();

        //clear folder index
        $i = 0;
        $imageIndex = 0;

        $this->deletePhotos("photos");
        mkdir("photos/");

        foreach ($photosArrsArr as $item) {

            //create dir for each photos set
            mkdir("photos/" . $i);
            foreach ($item as $url) {
                $image = file_get_contents($url);
                file_put_contents('photos/' . $i . '/' . $imageIndex++ . '.jpg', $image);
            }
            $imageIndex = 0;
            $i++;
        }

        $url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . 'photos.zip';

        echo "<a href='$url' target='_blank'>$url</a>";

        die();

    }

    private function deletePhotos ($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deletePhotos("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}