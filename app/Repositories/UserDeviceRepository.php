<?php

namespace App\Repositories;

use App\Models\UserDevice;
use App\Models\User;
use App\Models\UserMobileDeviceDummy;

class UserDeviceRepository {

    /**
     * Saves user's device information.
     *
     * @param integer $userId
     * @param string $deviceId
     * @param string $accessTokenId
     * @return UserDevice
     */
    public function save($userId, $deviceId, $accessTokenId) {
        // If there's a user device with device_id = $deviceId, then update it.
        // If no matching model exists, create one.
        return UserDevice::updateOrCreate(
                        [
                    'device_id' => $deviceId
                        ], [
                    'user_id' => $userId,
                    'access_token_id' => $accessTokenId,
   
                        ]
        );
    }
    
    /**
     * Saves user's device information.
     *
     * @param integer $userId
     * @param string $deviceId
     * @param string $accessTokenId
     * @return UserDevice
     */
    public function updateDeviceToken($userId, $deviceId, $accessTokenId) {
        // If there's a user device with device_id = $deviceId, then update it.
        // If no matching model exists, create one.
        return UserDevice::updateOrCreate(
                        [
                    'user_id' => $userId,
                    'access_token_id' => $accessTokenId
                        ], [
                    'device_id' => $deviceId
                        ]
        );
    }

    /**
     * Saves user's mobile number.
     *
     * @param integer $userId
     * @param string $deviceId
     * @param string $mobileNumber
     */
    public function saveMobileNumber($userId, $deviceId, $mobileNumber) {
        UserDevice::where('user_id', $userId)
                ->where('device_id', $deviceId)
                ->update([
                    'mobile_number' => $mobileNumber
        ]);
    }

    /**
     * Verifies user's mobile number.
     *
     * @param integer $userId
     * @param string $deviceId
     */
    public function verifyMobileNumber($userId, $deviceId) {
        UserDevice::where('user_id', $userId)
                ->where('device_id', $deviceId)
                ->update([
                    'mobile_number_verified' => true
        ]);
    }

     /**
     * Verifies user's mobile number.
     *
     * @param integer $userId
     * @param string $deviceId
     */
    public function verifyMobileNumberUser($userId, $deviceId) {
        User::where('id', $userId)  
                ->update([
                    'mobile_verified' => 1
        ]);
    }
    /**
     * Get List of user devices
     * 
     * @param type $userId
     * @return type
     */
    public function getUserDeviceList($userId) {
//        return UserDevice::where('user_id', $userId)->get();
        return UserDevice::where([['user_id', $userId]])->get();
    }
    /**
     * Get List of user devices by id
     * 
     * @param type $userId
     * @return type
     */
    
    public function getUserDeviceTokenById($userId){
        
       return UserDevice::where('user_id', $userId)->pluck('device_id')->toArray();
    }

    public function getUserDeviceByDeviceToken($deviceToken) {
        return UserDevice::where('access_token_id', $deviceToken)->first();
    }

    public function updateUserMobileVerification($user_id, $data) {
        return User::updateOrCreate(
                        [
                    'id' => $user_id
                        ], [
                    'mobile_verified' => true
                        ]
        );
    }

    /**
     * dummy records for the mobile number while while signup with the mobile number 
     * to send OTP number , save the  dummy entry of the mobile data
     * save device_id, mobile_number, 
     * added on 23rd January 2018
     */

    /**
     * Saves user's mobile number.
     *
     * @param integer $userId
     * @param string $deviceId
     * @param string $mobileNumber
     */
    public function saveMobileNumber_dummy($data, $code) {
        return UserMobileDeviceDummy::updateOrCreate(
                        [
                    'device_id' => $data['device_id']
                        ], [
                    'code' => $code,
                    'mobile_number' => $data['mobile_number'],
                    'mobile_number_verified' => '0',
                    'code_send_date' => date('Y-m-d H:i:s'),
                        ]
        );
    }

    public function match_update_dummy($device_id, $mobile_number, $verificationCode) {
        // Check if user's mobile number verification code matches with the given $verificationCode.
        $userMobileNumberVerificationCode = UserMobileDeviceDummy::where([
                    ['device_id', $device_id],
                    ['mobile_number', $mobile_number],
                    ['code', $verificationCode]
                ])->update([
            'mobile_number_verified' => true
        ]);

        return ($userMobileNumberVerificationCode != null) ? true : false;
    }

}
