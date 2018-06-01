<?php

namespace App\Services;

//use App\Repositories\UserRepository;
use App\Repositories\GroupRepository;
//use App\Repositories\UserProfilePicRepository;
//use App\Repositories\LayoverRepository;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorInstance;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ApplicationHelper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use App\Models\GroupMembers;
use App\Services\UserService;
use App\Repositories\FirebaseRepository;
use App\Repositories\SettingsRepository;
//use App\Models\Reactions;
use NotificationsHelper;

class GroupService {

    /**
     * The UserRepository object to handle database operations.
     *
     * @var UserRepository
     */
    private $groupRepository;

    /**
     * The groupUploadPath object to handle image path of group  
     * @var type 
     */
    private $groupUploadPath;

    /**
     * The Firebase object to handle database operations.
     *
     * @var UserDeviceRepository
     */
    private $fireBaseRepository;
    private $settingsRepository;
    private $userService;
    private $notification;

    /*

     * Constructs the GroupService object by injecting the required dependencies.
     *
     * @param GroupRepository $groupRepository
     */

    public function __construct(
    GroupRepository $groupRepository, FirebaseRepository $fireBaseRepository, SettingsRepository $settingsRepository, UserService $userService, NotificationsHelper $notification
    ) {
        $this->groupRepository = $groupRepository;
        $this->fireBaseRepository = $fireBaseRepository;
        $this->settingsRepository = $settingsRepository;
        $this->userService = $userService;
        $this->notification = $notification;
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
                    'email' => 'email|max:100',
                    'first_name' => 'required|max:100',
                    'last_name' => 'required|max:100',
                    'device_id' => 'required'
        ]);
    }

    /**
     * Saves user profile data in firebase.
     *
     * @param integer $userArray
     */
    public function saveFirebaseUser($users, $userDevice, $profilePic) {
        return $this->userRepository->saveFirebaseData($users, $userDevice, $profilePic);
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
     * Saves user's reaction information.
     *
     * @param type $userId
     * @param array $data
     */
//    public function saveReationInfo($userId, array $data) {
    public function createGroup($authUserId, array $data) {

        // group icon
        $data['group_icon'] = isset($data['group_icon']) ? $this->storeGroupIcon($data['group_icon'], 'group/group-icons') : '';
        // backgorund image
        $data['background_image'] = isset($data['background_image']) ? $this->storeGroupIcon($data['background_image'], 'group/group-backgrounds') : '';
        // save group data
        $saveData = $this->groupRepository->saveGroupData($data);

        $firebaseArray = $this->getFormattedGroupData($authUserId, $data, $saveData);
        // create converwsation id
        $conversationId = $this->fireBaseRepository->createFireBaseGroup($firebaseArray);

          $isAdminSettings = $this->settingsRepository->getValue();

         $status= ($isAdminSettings[0]->status == 1)?'P':'A';
          
        if (!empty($conversationId)) {
            $conversationId = json_decode($conversationId);

            // update group data
            $groupData = $this->groupRepository->updateGroupData($saveData->id, $conversationId->name);
            // save data in group members table 
            $firebaseToUserid = $data['user_id'];
            $fireBaseToArray = explode(',', $firebaseToUserid);
            $toUsers = [];
            foreach ($fireBaseToArray as $key => $val) {
                $toUsers[$key]['user_id'] = $val;
                $toUsers[$key]['status'] = $status;
                $toUsers[$key]['is_admin'] = 0;
                $toUsers[$key]['group_id'] = $groupData->id;
            }
            $finalArray = array_merge($toUsers, [['user_id' => $authUserId, 'is_admin' => 1, 
                'status'=>$status,'group_id' => $groupData->id]]);

            // save data in group members table 
            $groupMembers = $this->groupRepository->saveUserMemberData($finalArray);
            return $conversationId->name;
        }
    }

    public function createGroupMembers($authUserId, $conversationId, $data) {

        $firebaseArray = $this->getFormattedGroupDataConversation($authUserId, $data, $conversationId);
        $this->fireBaseRepository->setConversationData($conversationId, $firebaseArray);
        $fireBaseToArray = explode(',', $data['user_id']);

        foreach ($fireBaseToArray as $fireBaseToArrays) {

            $toUsers = User::find($fireBaseToArrays);
            $fromUser = User::find($authUserId);
            $profilePics = $this->userService->getUserProfilePic($fromUser->id);
            // send notification  method pass message,sender image,from user
            $fcmResponse = $this->notification->sendNotifications($toUsers, [
                'type' => 'createdgroupmembers',
                'notification_message' => \config('constants.NOTIFICATION_MESSAGE.SEND_NOTIFI_CHAT_GROUP'),
                'image' => $profilePics,
                'from_user' => $fromUser,
            ]);
        }
    }

    public function editGroup($authUserId, array $data) {

        $conversationId = $data['group_id'];
        // group icon
        if(isset($data['group_icon'])){
        $data['group_icon'] = $this->storeGroupIcon($data['group_icon'], 'group/group-icons');
        }
        // backgorund image
          if(isset($data['background_image'])){
        $data['background_image'] = $this->storeGroupIcon($data['background_image'], 'group/group-backgrounds');
          }        
// save group data
      
        $saveData = $this->groupRepository->editGroupData($data, $conversationId);
        if($saveData){

        $firebaseArray = $this->getFormattedGroupData($authUserId, $data, $saveData);

        $conversationId = $this->fireBaseRepository->updateFireBaseGroupData($conversationId, $firebaseArray);
        
        return $saveData;
        }
    }

    public function updateGroupMembers($authUserId, $data,$groupData) {

        $notifiedUsers=$this->groupRepository->editGroupMember($authUserId,$data,$groupData);
         $fromUser = $this->userService->getUserById($authUserId);
         
         $profilePics = $this->userService->getUserProfilePic($fromUser->id);
         // created members send notificatio
        if(isset($notifiedUsers['createdMembers']) && count($notifiedUsers['createdMembers'])>0){
                  
            foreach($notifiedUsers['createdMembers'] as $members){
          $toUsers=User::find($members);
 
          $fcmResponse = $this->notification->sendNotifications($toUsers, [
                'type' => 'createdgroupmembers',
                'notification_message' => \config('constants.NOTIFICATION_MESSAGE.SEND_NOTIFI_CHAT_GROUP'),
                'image' => $profilePics,
                'from_user' => $fromUser,
            ]);
           }
        }
        
        // deleted members send 
         if(isset($notifiedUsers['deletedMembers']) && count($notifiedUsers['deletedMembers'])>0){

           foreach($notifiedUsers['deletedMembers'] as $delmembers){
       
               $toDelUsers=User::find($delmembers['user_id']);
   
              $this->groupRepository->deleteGroupMemberById($delmembers['user_id'],$groupData->id);
        // send notification  method pass message,sender image,from user
                $fcmResponse = $this->notification->sendNotifications($toDelUsers, [
                    'type' => 'deletegroupmembers',
                    'notification_message' => \config('constants.NOTIFICATION_MESSAGE.SEND_NOTIFI_DELETE_GROUP_MEMBERS'),
                    'image' => $profilePics,
                    'from_user' => $fromUser,
                ]);
               }
           
           
          }
        $firebaseArray = $this->getFormattedEditGroupDataConversation($authUserId, $data, $data['group_id']);
        $this->fireBaseRepository->setConversationData($data['group_id'], $firebaseArray);
    }

    /**
     * save group icon
     * @param array $data
     * @param type $groupId
     * @param type $position
     * @return type
     */
    public function storeGroupIcon($fileName, $path) {
        // Generate the file name from current timestamp and file extension.

        $newFileName = Carbon::now()->timestamp . '.' . $fileName->extension();

        return Storage::disk('uploads')->putFileAs(// Save files inside 'uploads' storage disk using local driver
                        $path, // File name along with file path (user specific)
                        $fileName, // File contents
                        $newFileName // File name
        );
    }

    /**
     * save group background 
     * @param array $data
     * @param type $groupId
     * @param type $position
     * @return type
     */
    public function storeGroupBackground(array $data, $groupId = "", $position = "") {
        if (!empty($data['background_image'])) {
            $fileName = Carbon::now()->timestamp . '.' . $data['background_image']->extension();

            return Storage::disk('uploads')->putFileAs(// Save files inside 'uploads' storage disk using local driver
                            "group/group-backgrounds", // File name along with file path (user specific)
                            $data['background_image'], // File contents
                            $fileName // File name
            );
        } else {
            return "";
        }
    }

    /**
     * Gets group list data.
     *
     * @param type $us
     * @return boolean
     */
    public function getGroupByCovId($conv_id) {
        return $this->groupRepository->getGroupByCovId($conv_id);
    }

    /**
     * 
     * @param type $userId
     * @param type $groupId
     * @return type
     */
    public function checkGroupMemberExists($userId, $groupId) {
        return $this->groupRepository->checkGroupMemberExists($userId, $groupId);
    }

    /**
     * 
     * @param type $userId
     * @param type $groupId
     * @return type
     */
    public function saveGroupMemberRequest($userId, $groupId) {
        return $this->groupRepository->saveGroupMemberRequest($userId, $groupId);
    }

    public function getToUsers($authUserId, $data, $boolean) {
        $firebaseFromUserid = 'user_' . $authUserId;
        $firebaseToUserid = $data['user_id'];
        $fireBaseToArray = explode(',', $firebaseToUserid);
        $toUsers = [];
        foreach ($fireBaseToArray as $fireBaseToArrays) {
            $toUsers['user_' . $fireBaseToArrays] = $boolean;
        }
        $toUsers = array_merge($toUsers, [$firebaseFromUserid => $boolean]);
        return $toUsers;
    }

    /**
     * Gets formatted group members to send in API response.
     *
     * @param type $userId
     * @return array
     */
    public function getFormattedGroupData($authUserId, $data, $groupModel) {
        $firebaseFromUserid = 'user_' . $authUserId;
        $firebaseArray = [
            'admin' => $firebaseFromUserid,
            'photo' => $groupModel->getGroupIcon(),
        ];
        if (isset($data['user_id'])) {
            $toUsers = $this->getToUsers($authUserId, $data, 'true');
            $firebaseArray=array_merge($firebaseArray,['users'=>$toUsers ]);
        }
        if (isset($data['group_name'])) {
           $firebaseArray= array_merge($firebaseArray,['name'=>$data['group_name'] ]);
        }
 
          return $firebaseArray;
    }

    public function getFormattedGroupDataConversation($authUserId, $data, $conversionId) {
        $isAdminSettings = $this->settingsRepository->getValue();
        $firebaseFromUserid = 'user_' . $authUserId;
        $typingUsers = $this->getToUsers($authUserId, $data, 'false');
        $firebaseArray = [
            'Typing' => $typingUsers,
            'chat_history' => $this->getToUsers($authUserId, $data, ''),
            'isGroup' => 'true',
            'last_message' => [
                'conversationID' => $conversionId,
                'msgID' => '',
                'senderId' => $firebaseFromUserid,
                'senderName' => '',
                'text' => '',
                'timestamp' => '',
                'type' => 'text'
            ],
            'users' => $this->getToUsers($authUserId, $data, 'true'),
            'is_admin_setting' => $isAdminSettings[1]->status,
            'is_confirmed' => 1,
        ];
        return $firebaseArray;
    }

    public function getFormattedEditGroupDataConversation($authUserId, $data, $conversionId) {
        $isAdminSettings = $this->settingsRepository->getValue();
        $firebaseFromUserid = 'user_' . $authUserId;
        $typingUsers = $this->getToUsers($authUserId, $data, 'false');
        $firebaseArray = [
            'Typing' => $typingUsers,
            'chat_history' => $this->getToUsers($authUserId, $data, ''),
            'users' => $this->getToUsers($authUserId, $data, 'true'),
        ];
        return $firebaseArray;
    }

    /**
     * Group Join Request Notification 
     * 
     * @param type $userId
     * @param type $groupData
     * @return type
     */
    public function groupJoinRequestNotification($userId, $groupData) {
        return $this->groupRepository->groupJoinRequestNotification($userId, $groupData);
    }

    /**
     * 
     * @param type $queryData
     * @return type
     */
    public function getFormattedRequestContent($queryData) {
        $finalArray = [];
//        echo "<pre>";print_r($this->groupUploadPath = Storage::disk('public')->url('app/uploads'));exit;
        foreach ($queryData as $key => $val) {
            $array['id'] = $val->id;
            $array['group_id'] = $val->group_id;
            $array['group_fcm_id'] = $val->conversion_id;
            $array['user_id'] = $val->user_id;
            $array['name'] = $val->first_name . ' ' . $val->last_name;
            $array['email'] = $val->email;
            $array['storage_path'] = (!empty($val->storage_path) ? ($this->groupUploadPath . '/' . $val->storage_path) : "");
            $array['url'] = $val->url;
            $finalArray[] = $array;
        }

        return $finalArray;
    }

    /**
     * 
     * @param type $user_id
     * @param type $group_id
     * @return type
     */
    public function leaveGroup($authId, $conversationId) {
        $userId = 'user_' . $authId;
        $this->fireBaseRepository->deleteGroupData($conversationId . '/users/' . $userId);
        $this->fireBaseRepository->deleteConversationData($conversationId . '/Typing/' . $userId);
        $this->fireBaseRepository->deleteConversationData($conversationId . '/chat_history/' . $userId);
        $this->fireBaseRepository->deleteConversationData($conversationId . '/users/' . $userId);
        return $this->groupRepository->deleteGroupMember($authId, $conversationId);
    }

    /**
     * 
     * @param type $user_id
     * @param type $group_id
     * @return type
     */
    public function deleteGroupMember($user_id, $group_id) {

        return $this->groupRepository->deleteGroupMember($user_id, $group_id);
    }

    /**
     * 
     * @param type $user_id
     * @param type $group_id
     * @return type
     */
    public function deleteGroup($authId, $conversationId) {
        $this->fireBaseRepository->deleteGroupData($conversationId);
        $this->fireBaseRepository->deleteConversationData($conversationId);
        $this->fireBaseRepository->deleteMessagesdata($conversationId);

        return $this->groupRepository->deleteGroup($authId, $conversationId);
    }

    /**
     * get group detail
     * 
     * @param type $group_id
     * @return type
     */
    public function getGroupDetails($group_id) {
        return $this->groupRepository->getGroupDetails($group_id);
    }

    // get group conversation listing
    public function getConversationListing($userId){
          // conversation ID
         $conversationIds=$this->groupRepository->listGroup($userId);
 
         return $conversationIds;
       // return $this->groupRepository->listGroup($userId);
    }
}
