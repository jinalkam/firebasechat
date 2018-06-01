<?php

namespace App\Transformer;

use URL;
use Carbon\Carbon;
use App\Services\UserService;

class UserTransformer {

    private $userService;
    
   

    public function transform(User $user) {
        return [
            'name' => $user->name,
        ];
    }

    public function transformLogin($user,$apitoken,$profile_pics) {
        
        return [
            'user_id' => $user->id,
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'dob' => (!empty($user->dob)) ? Carbon::parse($user->dob)->format('Y-m-d') : '',
            'email' => $user->email ?? '',
            'gender' => $user->gender,
            'mobile_number' => $user->mobile_number ?? '',
            'mobile_verified' => (int)$user->mobile_verified ?? '',
            'profile_pic' =>$profile_pics,
          //  'profile_pic_thumbnail' => (!empty($user->profile_pic)) ? URL::to('/storage/app/uploads') . '/' . $user->profile_pic : "",
            'api_token' => $apitoken ?? '',
            
          
        ];
    }
    

    
    
    public function transformListing ($userArray,$firebaseArray){
       
            // array interstc key 
         $results=(array_intersect_key($firebaseArray,$userArray)); 
         
         
         return $results;
    }

}
