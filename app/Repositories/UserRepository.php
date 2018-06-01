<?php

namespace App\Repositories;

use DB;
use Storage;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\Settings_user;
use App\Models\Settings;
use App\Models\UserContacts;
use App\Models\Layover;
use SafeStudio\Firebase\Firebase;
use Carbon\Carbon;
use App\Helpers\ApplicationHelper;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\Supports;
use Illuminate\Support\Facades\Crypt;
use URL;

class UserRepository {
    /**
     * The CustomHelper object to handle database operations.
     *
     * @var CustomHelper
     */
//    private $customerHelper;
//
//    public function __construct(CustomHelper $customHelper) {
//        $this->customerHelper = $customHelper;
//    }

    /**
     * Creates a new user record in the database.
     *
     * @param array $data
     * @return App\Models\User $user
     * @throws \App\Repositories\Exception
     */
    public function create(array $data) {
        $user = new User;
        $user->fill($data);
        $user->save();
        return $user;
    }

    /**
     * Updates a user record in the database.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data) {

        $user->fill($data);
        $user->save();
        return $user;
    }

    /**
     * Gets user by facebook_id.
     *
     * @param string $facebookId
     * @return App\Models\User
     */
    public function getByFacebookId($facebookId) {
//        return User::where('facebook_id', $facebookId)->first();
        return User::where('facebook_id', $facebookId)->select('*', DB::raw('ROUND(DATEDIFF(now(), dob)/365.25) as age'))->first();
    }

    /**
     * Gets user by its id (primary key).
     *
     * @param type $userId
     * @return App\Models\User
     */
    public function getById($userId) {
        return User::find($userId);
        //  return User::where('id', $userId)->select('*', DB::raw('ROUND(DATEDIFF(now(), dob)/365.25) as age'), DB::raw("'U' as rec_type"))->first();
    }

    /**
     * formated firebase user data by its id (primary key).
     *
     * @param type $userId
     * @return App\Models\User
     */
    public function setFirebaseUser($userId) {
//        return User::find($userId);
        return User::find($userId);
    }

    /**
     * Updates user's last login time.
     *
     * @param type $userId
     * @param type $currentDateTime
     */
    public function updateLastLogin($userId, $currentDateTime) {
        User::where('id', $userId)
                ->update([
                    'last_login' => $currentDateTime
        ]);
    }

    public function getAllUser() {
        return User::select('*')
                        ->orderby('id', 'desc')
                        ->get();
    }

    public function getAllContacts() {
        return Supports::select('*', 'supports.created_at as support_query_date')
                        ->leftJoin('users', 'users.id', 'supports.user_id')
                        ->orderby('supports.created_at', 'desc')
                        ->get();
    }

    public function blockUser($userid) {
        $user = User::find($userid);
        $user->blocked = 1;
        $user->save();
    }

    public function unblockUser($userid) {
        $user = User::find($userid);
        $user->blocked = 0;
        $user->save();
    }

    /** remove device based on access_token_id 
     * 
     * @param type $userid
     */
    public function removeUserDeviceToken($userid) {
//        UserDevice::where('access_token_id', $userid)
//                ->delete();
        $userdevice = UserDevice::where('access_token_id', $userid)
                ->delete();
    }

    /**
     * Saves user's trust score data.
     *
     * @param type $userId
     * @param array $data
     * @return Users
     */
    public function save($userId, array $data): User {

        // If there's a user layover with user_id = $userId, then update it.
        // If no matching model exists, create one.
        return User::updateOrCreate(
                        [
                    'id' => $userId
                        ], $data
        );
    }

    /**
     * Return the list  of interest id from the interest user table 
     * 
     * @param type $userid
     * @return type
     */
    public function getGeneralPreferenceId($userid) {
        return DB::table('interest_user')->where('user_id', $userid)->pluck('interest_id')->all();
    }

    /**
     * List the language id from the language user table 
     * 
     * @param type $userid
     * @param type $type
     * @return type
     */
    public function getLanguagePreferenceList($userid, $type) {
        return DB::table('language_user')->where([['user_id', $userid], ['type', $type]])->pluck('language_id')->all();
    }

    /**
     * Get users all setting values
     * 
     * @param type $userid
     * @return type
     */
    public function getUsersSettings($userid) {
        return DB::table('settings_users')
                        ->leftjoin('settings', 'settings_users.settings_id', 'settings.id')
                        ->where('settings_users.user_id', $userid)
                        ->orderby('settings_id', 'asc')
                        ->get();
//        return DB::table('settings_users')->where('user_id', $userid)->get();
    }

    /**
     * Check phone number already registered with the account or not.
     * 
     * @param type $userPhone
     * @return type
     */
    public function checkVerifiedPhoneNumber($userPhone) {
        return User::where('mobile_number', $userPhone)->where('mobile_verified', 1)->select('*')->first();
    }

    ################ Custom code for signup with email address

    public function checkEmailExistsOrNot($userEmail) {
        return User::where('email', $userEmail)->select('*')->first();
//        return User::where('id', $userId)->select('*', DB::raw('ROUND(DATEDIFF(now(), dob)/365.25) as age'), DB::raw("'U' as rec_type"))->first();
    }

    public function checkValidLoginCredentials($data) {
//        $password = Crypt::encryptString($data['password']);
//        $password = base64_encode($data['password']);
        $password = ApplicationHelper::encryptPass($data['password']);
        DB::enableQueryLog();
        return User::where([
                            ['email', $data['email']],
                            ['password', $password],
//                            ['status', 'A']
                        ])
                        ->select('*')
                        ->first();
    }

    public function checkValidPhoneNumberLoginCredentials($data) {
//        $password = base64_encode($data['password']);
        $password = ApplicationHelper::encryptPass($data['password']);
        DB::enableQueryLog();
        return User::where([
                            ['mobile_number', $data['mobile_number']],
                            ['password', $password],
                        ])
                        ->select('*')
                        ->first();
    }

    /**
     * Verifies user's mobile number.
     *
     * @param integer $userId
     * @param string $deviceId
     */
    public function verifyMobileNumberUser($userId) {
        $user = User::where('id', $userId)
                ->update([
            'mobile_verified' => 1
        ]);
        if ($user) {
            return true;
        }
        return false;
    }

    public function matchContact($userId, $contacts) {

        //delete the users 
        UserContacts::where('user_id', $userId);
        DB::update("ALTER TABLE user_contacts AUTO_INCREMENT = 1;");
        $i = 0;
        $userStrings = [];
        foreach ($contacts as $key => $val) {

            // get the users
            $user = User::select('id', 'mobile_number', 'first_name', 'last_name')
                    ->where('mobile_number', $val->c_mobile_no)
                    ->where('mobile_verified', '=', 1)
                    ->where('id', '!=', $userId)
                    ->first();
            if ($user) {
                $userStrings[$i]['id'] = $user->id;
                $userStrings[$i]['c_contact_name'] = $val->c_contact_name;
                $userStrings[$i]['c_contact_number'] = $val->c_mobile_no;
                $userStrings[$i]['full_name'] = $user->getFullname();
                $userStrings[$i]['profile_pic'] = (!empty($user->profilePics[0]->storage_path)) ? URL::to('/storage/app/uploads') . '/' . $user->profilePics[0]->storage_path : "";
                $i++;
            }
        }

        return $userStrings;
    }

    public function saveContact($userId, $data, $confirmed) {

        $finalUsers = [];
        if (count($data) > 0) {

            foreach ($data as $users) {

                $contacts = UserContacts::where('user_id', $users['id'])->where('contact_user_id', $userId)->get();
                if (count($contacts) == 0) {

                    $conversionId = 'user' . $userId . '_' . 'user' . $users['id'];
                    $finalUsers[] = UserContacts::updateOrcreate(['conversation_id' => $conversionId], ['user_id' => $userId, 'contact_user_id' => $users['id'],
                                'conversation_id' => $conversionId, 'status' => 'A',
                                'is_confirmed' => $confirmed]);
                }
            }
        }

        return $finalUsers;
    }

    public function getConfirmedStatus($id) {

        $user = UserContacts::where('user_id', $id)->first();

        if (!empty($user)) {
            return $user->is_confirmed;
        }
        return 0;
    }

    public function saveConfirmStatus($conversationId, $status) {

        $user = UserContacts::where('conversation_id', $conversationId)->first();
        $user->is_confirmed = $status;
        $user->save();
        return $user;
    }

    public function getUserContactDataByConversationId($id) {

        $user = UserContacts::where('conversation_id', $id)->first();

        if (!empty($user)) {
            return $user;
        }
        return false;
    }

    //*************************************************//
    // * @name   : index
    // * @todo   : Delete the reord of user
    // * @params :  
    // * @Date   : 21-April-2018
    //************************************************//

    public function deleteUser($id = NULL) {
        $user = User::find($id)->delete();
        return $user;
    }

    // list the one to one chat users
    public function listUserContacts($authId) {

        $results = UserContacts
                ::where('is_confirmed', 1)
                ->where(function($q) use ($authId) {
                    $q->where('user_id', $authId)
                    ->orWhere('contact_user_id', $authId);
                })->pluck('conversation_id', 'conversation_id')
                ->toArray();


        return $results;
    }

}
