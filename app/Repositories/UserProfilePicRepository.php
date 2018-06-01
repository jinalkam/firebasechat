<?php

namespace App\Repositories;

use App\Models\UserProfilePic;

class UserProfilePicRepository {

    /**
     * Saves user's profile pic data in the database.
     *
     * @param array $data
     */
    public function save(array $data) {
        $userProfilePic = new UserProfilePic;
        $userProfilePic->fill($data);
        $userProfilePic->save();
    }

    /**
     * Saves user's profile pic data in the database.
     *
     * @param array $data
     */
    public function update($picId, array $data) {
        UserProfilePic::updateOrCreate(['id' => $picId], $data);
    }
    
     /**
     * Saves user's single profile pic data in the database.
     *
     * @param array $data
     */
    public function updateSingleImage($data) {
        UserProfilePic::updateOrCreate(['user_id' => $data['user_id']], $data);
    }
    
    /**
     * Gets user's profile pic for the particular position.
     *
     * @param integer $userId
     * @param integer $position
     * @return UserProfilePic|null If found, it returns an instance of UserProfilePic, null otherwise.
     */
    public function getByPosition($userId, $position) {
        return UserProfilePic::where('user_id', $userId)
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
        return UserProfilePic::where('user_id', $userId)
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
        return UserProfilePic::where('user_id', $userId)
                        ->get();
    }

}
