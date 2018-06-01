<?php

namespace App\Http\Transformer;

use URL;

use Auth;

use \App\Http\Services\UserTrait;

class TagTransformer {

 
    use UserTrait;

  

    public function transformAllUsers($data) {

        $var = [];
        $var = $data->map(function ($item) {

            return [
                'user_id' => $item->id ?? '',
                'full_name' =>  $item->userdetail->first_name . " " . $item->userdetail->last_name ?? '',
                'profile_image' => (!empty($item->userdetail->profile_pic)) ? URL::to('/storage/app/public/profile_pic') . '/' . $item->userdetail->profile_pic : "",
            ];
        });
        return $var;
    }

   

}
