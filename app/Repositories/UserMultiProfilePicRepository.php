<?php

namespace App\Repositories;

use App\Models\UserMultiProfilePic;

class UserMultiProfilePicRepository {

    /**
     * Saves user's profile pic data in the database.
     *
     * @param array $data
     */
    public function save(array $data) {
        $userProfilePic = new UserMultiProfilePic;
        $userProfilePic->fill($data);
        $userProfilePic->save();
    }

    /**
     * Saves user's profile pic data in the database.
     *
     * @param array $data
     */
    public function update($picId, array $data) {
        UserMultiProfilePic::updateOrCreate(['id' => $picId], $data);
    }
    

    
    /**
     * Gets user's profile pic for the particular position.
     *
     * @param integer $userId
     * @param integer $position
     * @return UserProfilePic|null If found, it returns an instance of UserProfilePic, null otherwise.
     */
    public function getByPosition($userId, $position) {
        return UserMultiProfilePic::where('user_id', $userId)
                        ->where('position_index', $position)
                        ->first();
    }

    /**
     * Gets user's profile pic for the particular position.
     *
     * @param integer $userId
     * @param integer $profilePicId
     * @return UserProfilePic|null If found, it returns an instance of UserProfilePic, null otherwise.
     */
    public function getByProfilePicId($userId, $profilePicId) {
        return UserMultiProfilePic::where('user_id', $userId)
                        ->where('id', $profilePicId)
                        ->first();
    }

    /**
     * Gets user's profile pic for the particular position.
     *
     * @param integer $userId
     * @param integer $position
     * @return UserProfilePic|null If found, it returns an instance of UserProfilePic, null otherwise.
     */
    public function getAllImages($userId) {
        return UserMultiProfilePic::where('user_id', $userId)
                        ->get();
    }
    
    public function deleteProfilePics($userId){
           return UserMultiProfilePic::where('user_id', $userId)
                        ->delete();
    }
    
     /**
     * Gets user's profile pic for the particular position.
     *
     * @param integer $userId
     * @param integer $position
     * @return UserProfilePic|null If found, it returns an instance of UserProfilePic, null otherwise.
     */
    public function getImages($number) {
        return UserMultiProfilePic::orderBy('id', 'desc')->take($number)->get();
    }
    

}
