<?php

namespace App\Services;

//use Storage;
use App\Repositories\UserRepository;
use App\Repositories\UserDeviceRepository;
use App\Repositories\UserProfilePicRepository;
use App\Repositories\UserMultiProfilePicRepository;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorInstance;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ApplicationHelper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Repositories\FirebaseRepository;
use App\Repositories\SettingsRepository;
use Auth;
use URL;
use NotificationsHelper;

class UserService {

    /**
     * The UserRepository object to handle database operations.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * The FirebaseRepository object to handle firebase operations.
     *
     * @var FirebaseRepository
     */
    private $firebaseRepository;

    /**
     * The SettingsRepository object to handle settings operations.
     *
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * The NotificationRespository object to handle notification operations.
     *
     * @var SettingsRepository
     */
    private $notification;
    
    private $userMultiProfilePicRepository;

    /**
     * Constructs the UserService object by injecting the required dependencies.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(
    UserRepository $userRepository, UserDeviceRepository $userDeviceRepository,
            UserProfilePicRepository $userProfilePicRepository, 
            FirebaseRepository $firebaseRepository, 
            SettingsRepository $settingsRepository, 
            userMultiProfilePicRepository $userMultiProfilePicRepository,
            NotificationsHelper $notification) {
        $this->settingsRepository = $settingsRepository;
        $this->userRepository = $userRepository;
        $this->firebaseRepository = $firebaseRepository;
        $this->userDeviceRepository = $userDeviceRepository;
        $this->userProfilePicRepository = $userProfilePicRepository;
        $this->userMultiProfilePicRepository=$userMultiProfilePicRepository;
        $this->notification = $notification;
        $this->fileUploadPath = Storage::disk('public')->url('app/uploads');
    }

    /**
     * Validates create user account data (in case of first time login with Facebook).
     *
     * @param array $data
     * @return ValidatorInstance
     */
    public function validateCreateAccountData(array $data): ValidatorInstance {
        return Validator::make($data, [
                    'facebook_id' => 'required|max:100',
                    // 'first_name' => 'required|max:100',
                    // 'last_name' => 'required|max:100',
                    'device_id' => 'required',
                    // 'email'=>'sometimes|unique:users,email',
                    'type' => 'required'
        ]);
    }

    /**
     * Validates conversation id and is_accept paramter.
     *
     * @param array $data
     * @return ValidatorInstance
     */
    public function validateInviteAcceptData(array $data): ValidatorInstance {
        return Validator::make($data, [
                    'conversation_id' => 'required',
                    'is_confirmed' => 'required',
        ]);
    }

    /**
     * Validates create user account data (in case of first time login with Facebook).
     *
     * @param array $data
     * @return ValidatorInstance
     */
    public function validateCustomCreateAccountData(array $data): ValidatorInstance {
        return Validator::make($data, [
//                    'facebook_id' => 'required|max:100',
                    'email' => 'email|max:100',
                    'password' => 'required|max:100',
                    'first_name' => 'required|max:100',
                    'last_name' => 'required|max:100',
                    'device_id' => 'required'
        ]);
    }

    /**
     * Gets user by facebook id.
     *
     * @param string $facebookId
     * @return App\Models\User
     */
    public function getByFacebookId($facebookId, $data) {
        $user = $this->userRepository->getByFacebookId($facebookId);
        // Creating and issueing access token to the user.       
        if ($user) {
            // update user profile when second time login.
            $user = $this->updateProfile($user, $data);
        } else {
            // Create user account.
            $user = $this->createProfile($data);
        }
        return $user;
    }

    /**
     * Creates user account.
     *
     * @param  array $data
     * @return \App\Models\User $user
     */
    public function createProfile(array $data) {
        // Since user is logged in with Facebook 'facebook_verified' is true.
        // Create user account using UserRepository.
        $user = $this->userRepository->create($data);

        return $user;
    }

    /**
     * Updates user profile data.
     *
     * @param User $user
     * @param array $data
     */
    public function updateProfile(User $user, array $data) {
        if (isset($data['mobile_number']) && $user->mobile_verified == 1) {
            unset($data['mobile_number']);
        }
        $user = $this->userRepository->update($user, $data);

        return $user;
    }

    /**
     * Format DateOfBirth fetched from facebook
     * 
     * @param type $date
     * @return type
     */
    public function formatDOB($date) {
        if (!empty($date)) {
            return date("Y-m-d", strtotime($date));
        } else {
            return null;
        }
    }

    /**
     * Saves user profile data in firebase.
     *
     * @param integer $userArray
     */
    public function saveFirebaseUser($users, $userDevice, $profilePic) {
        return $this->userRepository->saveFirebaseData($users, $userDevice, $profilePic);
    }

    public function updateFirebaseUserData($userid, $authUsers) {
        $user = $this->userRepository->getById($userid);
        $userDeviceId = $authUsers['accessToken']->id;
        $userDevice = $this->userDeviceRepository->getUserDeviceByDeviceToken($userDeviceId);
        $profile_pics = $this->getUserProfilePics($userid);
        // finally store user data in firebase database.
        return $this->saveFirebaseUser($user, $userDevice, $profile_pics);
    }

    /**
     * Creates and issues app access token to the user.
     *
     * @param \App\Models\User $user
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function issueAccessToken(User $user) {
        return $user->createToken('codemine');
    }

    /**
     * Revokes app access token issued to the user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function revokeAccessToken(User $user) {
        // remove fcm token from firebase table and database

        $this->userRepository->removeUserDeviceToken($user->token()->id);
        //  revoke token to logout - passport
        $user->token()->revoke();

        $this->updateFirebaseDeviceToken($user);
    }

    /**
     * Updates last login time of the user.
     *
     * @param type $userId
     */
    public function updateLastLogin($userId) {
        $this->userRepository->updateLastLogin($userId, $this->currentDateTime);
    }

    /**
     * Saves user's device information.
     *
     * @param integer $userId
     * @param string $deviceId
     * @param string $accessTokenId
     */
    public function saveDeviceInfo($userId, $deviceId, $accessToken) {
        
        $this->userDeviceRepository->save($userId, $deviceId, $accessToken);
        $users = $this->userRepository->setFirebaseUser($userId);
        $this->updateFirebaseDeviceToken($users);
    }

    /**
     * Update fireabse device token.
     *
     * @param integer $users
     */
    public function updateFirebaseDeviceToken($users) {

        if ($users->mobile_verified == 1) {
            $device = [];
            if (isset($users->devices)) {

                foreach ($users->devices as $devices) {

                    $device[] = $devices->device_id;
                }
            }

            $firebaseArray = [
                'id' => $users->id,
                'last_seen' => (string) (Carbon::now()->timestamp * config('constants.mili_to_micro_second')),
                'name' => $users->first_name . ' ' . $users->last_name,
                'token' => $device
            ];
            // create firebase user
            $this->firebaseRepository->createFireBaseUser($firebaseArray);
        }
    }

    /**
     * Saves user's device information.
     *
     * @param integer $userId
     * @param string $deviceId
     * @param string $accessTokenId
     */
    public function getDeviceInfoById($userId) {

        return $this->userDeviceRepository->getUserDeviceList($userId);
    }

    /**
     * Saves user's device information.
     *
     * @param integer $userId
     * @param string $deviceId
     * @param string $accessTokenId
     */
    public function updateDeviceInfo($userId, $deviceId, $accessToken) {

        return $this->userDeviceRepository->updateDeviceToken($userId, $deviceId, $accessToken);
    }

    /**
     * Validates save profile pic data.
     *
     * @param array $data
     * @return ValidatorInstance
     */
    public function validateSaveProfilePicData(array $data): ValidatorInstance {
        return Validator::make($data, [
                    'position_index' => 'required|integer',
                    'profile_pic' => 'required|file|image',
        ]);
    }

    /**
     * Save user's profile pic on 'uploads' storage disk using local driver.
     *
     * @param integer $userId
     * @param array $data
     * @return string Returns stored file path.
     */
    public function storeProfilePic($userId, $data, $position = "") {

        // Generate the file name from current timestamp and file extension.
        if (!empty($position)) {
            $fileName = Carbon::now()->timestamp . '_.' . $position . '.' . $data->file('profile_pic')->extension();
        } else {
            $fileName = Carbon::now()->timestamp . '.' . $data->file('profile_pic')->extension();
        }

        $storedFilePath = Storage::disk('uploads')->putFileAs(// Save files inside 'uploads' storage disk using local driver
                "user/$userId/profile-pics", // File name along with file path (user specific)
                $data->file('profile_pic'), // File contents
                $fileName // File name
        );

        // Save profile pic data in the database.
        if ($storedFilePath) {
            return $this->saveProfilePic([
                        'user_id' => $userId,
                        'storage_path' => $storedFilePath,
                        'position_index' => 1
            ]);
        }

        return false;
    }

    /**
     * Saves user single profile pic data in the database.
     *
     * @param array $data
     */
    public function saveProfilePic($data) {
        $this->userProfilePicRepository->updateSingleImage($data);
    }
 /**
     * Saves user single profile pic data in the database.
     *
     * @param array $data
     */
    public function saveMultiProfilePic($data) {
        $this->userMultiProfilePicRepository->save($data);
    }
    
    
    /**
     * Gets profile pic data.
     *
     * @param type $userId
     * @param type $position
     * @return boolean
     */
    public function getProfilePicData($userId, $position) {
//        echo "<pre>";print_r((Carbon::now()->timestamp) * 100);
//        echo "<pre>";(Carbon::now()->tz);exit;
        // Check if profile pic exists for this position.
//        $profilePic = $this->userProfilePicRepository->getByPosition($userId, $position);
        $profilePic = $this->userProfilePicRepository->getByProfilePicId($userId, $position);

        if (!$profilePic) {
            return false;
        }

        // Check if the file is present on the storage.
        $path = ApplicationHelper::diskRootPath('uploads') . '/' . $profilePic->storage_path;

        $fileSystem = new Filesystem;
        if (!$fileSystem->exists($path)) {
            return false;
        }

        // If all good then send the file contents with its mime-type.
        return [
            'contents' => $fileSystem->get($path),
            'mimeType' => $fileSystem->mimeType($path)
        ];
    }

    /**
     * Gets multiple profile pic data.
     *
     * @param type $us
     * @return boolean
     */
    public function getUserProfilePics($userId) {
        // Check if profile pic exists for this position.
        $profilePic = $this->userMultiProfilePicRepository->getAllImages($userId);
        $fileArray = [];

        if (!$profilePic) {
            return $fileArray;
        }

        $fileSystem = new Filesystem;
        foreach ($profilePic as $key => $prof_val) {
            // Check if the file is present on the storage.
           
            
            $fileArray[] = [
//                'contents' => $fileSystem->get($path),
//                'contents' => $path,
                'pic_id' => $prof_val->id,
                'url' => (!empty($prof_val->storage_path)) ? URL::to('/storage/app/uploads') . '/' . $prof_val->storage_path : "",
                //'position' => $prof_val->position_index,
//                'mimeType' => $fileSystem->mimeType($path)
            ];

            // If all good then send the file contents with its mime-type.
        }
        return $fileArray;
    }

    // check verifed phone number
    public function checkVerifiedPhoneNumber($userPhone) {
        return $this->userRepository->checkVerifiedPhoneNumber($userPhone);
    }

    public function checkValidPhoneNumberLoginCredentials(array $data) {
        return $this->userRepository->checkValidPhoneNumberLoginCredentials($data);
    }

    /**
     * Gets single profile pic data.
     *
     * @param type $us
     * @return boolean
     */
    public function getUserProfilePic($userId) {

        $profilePic = $this->userProfilePicRepository->getAllImages($userId);
        return (!empty($profilePic[0]->storage_path)) ? URL::to('/storage/app/uploads') . '/' . $profilePic[0]->storage_path : "";
    }

    /**
     * Gets layover list data.
     *
     * @param type $us
     * @return boolean
     */
    public function getUserById($userid) {
        return $this->userRepository->getById($userid);
    }

    /**
     * create conversation in firebase of all users matched
     *
     * @param integer $data
     * @return boolean Returns null.
     */
    public function createConversation($userId, $user) {

        if (!empty($user)) {
            $isAdminSettings = $this->settingsRepository->getValue();


            foreach ($user as $users) {

                if ($isAdminSettings[1]->status == 0) {
                    $confirmed = 1;
                } else {
                    $confirmed = $this->userRepository->getConfirmedStatus($users->contact_user_id);
                }
                $firebaseFromUserid = 'user_' . $userId;
                $firebaseToUserid = 'user_' . $users->contact_user_id;
                $conversionId = 'user' . $userId . '_' . 'user' . $users->contact_user_id;
                $firebaseArray = [
                    'Typing' => [
                        $firebaseFromUserid => 'false',
                        $firebaseToUserid => 'false',
                    ],
                    'chat_history' => [
                        $firebaseFromUserid => '',
                        $firebaseToUserid => '',
                    ],
                    'isGroup' => 'false',
                    'last_message' => [
                        'conversationID' => $conversionId,
                        'msgID' => '',
                        'senderId' => $firebaseFromUserid,
                        'senderName' => '',
                        'text' => '',
                        'timestamp' => '',
                        'type' => 'text'
                    ],
                    'users' => [
                        $firebaseFromUserid => 'true',
                        $firebaseToUserid => 'true',
                    ],
                    'is_admin_setting' => $isAdminSettings[1]->status,
                    'is_confirmed' => $confirmed,
                ];
                // pass from and to id for conversation
                $this->firebaseRepository->setConversationData($conversionId, $firebaseArray);
            }
        }
    }

    /**
     * update conversation in firebase of all users matched
     *
     * @param integer $data
     * @return boolean Returns null.
     */
    public function updateConversation($data) {

        $firebaseArray = ['is_confirmed' => (int) $data['is_confirmed']];
        // from user
        $fromUser = $this->getUserById(Auth::id());
        $getUserContact = $this->userRepository->saveConfirmStatus($data['conversation_id'], $data['is_confirmed'])->user_id;
        $toUser = $this->getUserById($getUserContact);
        $profilePics = $this->getUserProfilePic($fromUser->id);
        $this->userRepository->saveConfirmStatus($data['conversation_id'], $data['is_confirmed']);
        // pass from and to id for conversation
        if ($data['is_confirmed'] == 1) {
            $this->firebaseRepository->setConversationData($data['conversation_id'], $firebaseArray);

            // send notification  if user has accepted the conversation
            $fcmResponse = $this->notification->sendNotifications($toUser, [
                'type' => 'acceptinvite',
                'notification_message' => \config('constants.NOTIFICATION_MESSAGE.USER_ACCEPT'),
                'image' => $profilePics,
                'from_user' => $fromUser,
            ]);
        } else {
            $this->firebaseRepository->deleteConversationData($data['conversation_id']);
            // send notification  if user has rejected the conversation
            $fcmResponse = $this->notification->sendNotifications($toUser, [
                'type' => 'rejectinvite',
                'notification_message' => \config('constants.NOTIFICATION_MESSAGE.USER_REJECT'),
                'image' => $profilePics,
                'from_user' => $fromUser,
            ]);
        }
    }

    /**
     * get all users listing 
     *
     * @param integer $data
     * @return boolean Returns json.
     */
    public function getAllUsers() {
        $Userlist = $this->userRepository->getAllUser()->toArray();
        return $Userlist;
    }

    /**
     * get deleted user
     *
     * @param integer $data
     * @return boolean Returns json.
     */
    public function deleteUser($id = NULL) {
        if (Auth::guard('admin')->user()->id == $id) {
            $result = 'loginuser';
        } else {
            $result = $this->userRepository->deleteUser($id);
        }
        return $result;
    }

    /**
     * get convesation id of given user
     *
     * @param integer $data
     * @return boolean Returns json.
     */
    public function getConversationListing($userId) {

        return $this->userRepository->listUserContacts($userId);
    }

    public function setNotification($userId, $data) {

        return $this->settingsRepository->setSettingsNotification($userId, $data);
    }
    
    public function getFirebaseData($userId)
    {
        // get conversion listing
         $conversation= $this->firebaseRepository->listConversationListing();
         //json decoded
         $conversationDecode= json_decode($conversation,true);
         $final=[];
         foreach($conversationDecode as $key1=>$val1)
         {
  
             if($val1['isGroup']=='false'){
                 foreach($val1['users'] as $key=>$val){
             
                     $userExploded=explode("_",$key);
                     if($userExploded[1] != $userId ){
                         $userName=$this->getUserById($userExploded[1]);
                         if($userName){
                            $fullName=$userName->getFullname();
                         }else{
                             $fullName="";
                         }
                       
                     }
                 }
             
               $final[$key1]=array_merge($val1,['reciverName'=>$fullName]);
             }else{
                  $final[$key1]=$val1;
             }
         }
    
         return $final;
    }
    
    public function storeMultiProfilePics($userId, $data) {
        // delete profile pics
        $this->userMultiProfilePicRepository->deleteProfilePics($userId);
         DB::update("ALTER TABLE user_multi_profile_pics AUTO_INCREMENT = 1;");
            foreach ($data as $key => $val) {

                $position = ($key + 1);
                // Save user's profile pic on 'uploads' storage disk using local driver.
                $storedFilePath = $this->storeMultiProfilePic($userId, $val, $position);
            }
        
    }
    
     /**
     * Save user's profile pic on 'uploads' storage disk using local driver.
     *
     * @param integer $userId
     * @param array $data
     * @return string Returns stored file path.
     */
    public function storeMultiProfilePic($userId, $data, $position = "") {

        // Generate the file name from current timestamp and file extension.
//        if (!empty($position)) {
//         
//            $fileName = Carbon::now()->timestamp . '_' . $position . '.' . $data->extension();
//        } else {
//            $fileName = Carbon::now()->timestamp . '.' . $data->extension();
//        }

        $storedFilePath = Storage::disk('uploads')->putFileAs(// Save files inside 'uploads' storage disk using local driver
                "user/$userId/profile-pics", // File name along with file path (user specific)
                $data, // File contents
                $data->getClientOriginalName() // File name
        );

        // Save profile pic data in the database.
        if ($storedFilePath) {
            return $this->saveMultiProfilePic([
                        'user_id' => $userId,
                        'storage_path' => $storedFilePath,
                        'position_index' => $position
            ]);
        }

        return false;
    }
    
    
     /**get last 10 profiel pic images.
     *
     * @param integer $userId
     * @param array $data
     * @return string Returns stored file path.
     */
     public function getProfilePics($number) {
        // Check if profile pic exists for this position.
        $profilePic = $this->userMultiProfilePicRepository->getImages($number);
        $fileArray = [];

        if (!$profilePic) {
            return $fileArray;
        }

        $fileSystem = new Filesystem;
        foreach ($profilePic as $key => $prof_val) {
            // Check if the file is present on the storage.
           
            
            $fileArray[] = [
//                'contents' => $fileSystem->get($path),
//                'contents' => $path,
                'pic_id' => $prof_val->id,
                'url' => (!empty($prof_val->storage_path)) ? URL::to('/storage/app/uploads') . '/' . $prof_val->storage_path : "",
                //'position' => $prof_val->position_index,
//                'mimeType' => $fileSystem->mimeType($path)
            ];

            // If all good then send the file contents with its mime-type.
        }
        return $fileArray;
    }
}
