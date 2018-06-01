<?php

namespace App\Repositories;

use App\Models\UserMobileNumberVerificationCode;

class UserMobileNumberVerificationCodeRepository
{
    /**
     * Saves user's mobile number verification code.
     *
     * @param integer $userId
     * @param string $verificationCode
     */
    public function save($userId, $verificationCode) {
        // If there's a verification code for the user with user_id = $userId, then update it.
        // If no matching model exists, create one.
        return UserMobileNumberVerificationCode::updateOrCreate(
            [
                'user_id' => $userId
            ],
            [
                'code' => $verificationCode
            ]
        );
    }

    /**
     * Matches user's verification code.
     *
     * @param integer $userId
     * @param string $verificationCode
     * @return boolean Returns true if the code matches, false otherwise.
     */
    public function match($userId, $verificationCode) {
        // Check if user's mobile number verification code matches with the given $verificationCode.
        $userMobileNumberVerificationCode =
                UserMobileNumberVerificationCode::where('user_id', $userId)
                    ->where('code', $verificationCode)
                    ->first();

        return ($userMobileNumberVerificationCode != null) ? true : false;
    }
}
