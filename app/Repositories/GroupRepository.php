<?php

namespace App\Repositories;

use DB;
use Storage;
use App\Models\Groups;
use App\Models\User;
use App\Models\GroupMembers;
use App\Repositories\UserRepository;
use App\Repositories\UserDeviceRepository;
use SafeStudio\Firebase\Firebase;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\APIs\V1\UserController;
use App\Helpers\CustomHelper;

class GroupRepository {

    private $customerHelper;

    /**
     * The UserRepository object to handle database operations.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * The UserDeviceRepository object to handle database operations.
     *
     * @var UserDeviceRepository
     */
    private $userDeviceRepository;

    public function __construct(
    UserDeviceRepository $userDeviceRepository, UserRepository $userRepository, CustomHelper $customHelper
    ) {
//        $this->customerHelper = new CustomHelper();
        $this->customerHelper = $customHelper;
        $this->userRepository = $userRepository;
        $this->userDeviceRepository = $userDeviceRepository;
    }

    /**
     * Gets user by its conversation id (FCM primary id).
     *
     * @param type $userId
     * @return App\Models\User
     */
    public function getGroupId($id) {
        return Groups::find($id);
    }

    /**
     * Gets user by its conversation id (FCM primary id).
     *
     * @param type $userId
     * @return App\Models\User
     */
    public function getGroupByCovId($convId) {
        return Groups::where('conversion_id', $convId)->first();
    }

    public function getAllGroups() {
        return Groups::select('*')
                        ->orderby('id', 'desc')
                        ->get();
    }

    /**
     * get the group members list based on group id with user detail 
     * @param type $group_id
     * @return type
     */
    public function getGroupMemberUsersData($group_id) {
        //$this->groupUploadPath = Storage::disk('public')->url('app/uploads');
        $result = GroupMembers::where([
                            ['group_id', $group_id],
                            ['group_members.status', 'A']
                        ])
                        ->select('group_members.id as id', 'group_members.user_id', 'group_members.group_id', 'group_members.is_admin', 'group_members.status', 'users.first_name', 'users.last_name', 'users.gender', 'users.description', 'users.trust_level', 'user_profile_pics.storage_path', 'user_profile_pics.url')
                        ->leftJoin('users', 'users.id', '=', 'group_members.user_id')
                        ->leftJoin('user_profile_pics', 'user_profile_pics.user_id', '=', 'users.id')
                        ->groupBy('group_members.user_id')->get();
        $returnData = GroupRepository::getFormattedGroupMembersProfilePic($result);

        return $returnData;
//        if (Input::get('storage_path') != ""){
//            $Select_db->select('storage_path', Input::get('storage_path'));
//        } else {
//            $Select_db->select('storage_path', Input::get('storage_path'));
//        }
//        $result = $Select_db->get();
    }

    /**
     * get storagePath in group members data for user profile pic
     * @param type $data
     * @return string
     */
    public function getFormattedGroupMembersProfilePic($data) {
        $finalArray = [];
        foreach ($data as $key => $val) {
            if (isset($val['storage_path']) && !empty($val['storage_path'])) {
                $val['storage_path'] = Storage::disk('public')->url('app/uploads') . '/' . $val['storage_path'];
            }
            $finalArray[] = $val;
        }
        return $finalArray;
    }

    /**
     * 
     * @param type $userId
     * @param type $groupId
     * @return type
     */
    public function checkGroupMemberExists($userId, $groupId) {
//        DB::enableQueryLog();
        return GroupMembers::where([
                    [
                        'user_id', $userId
                    ], [
                        'group_id', $groupId
                    ]
                ])->first();
//        $data = DB::getQueryLog();
    }

    /**
     * Save Group members request for joining
     * 
     * @param type $userId
     * @param type $groupId
     * @return type
     */
    public function saveGroupMemberRequest($userId, $groupId) {
        GroupRepository::saveUserGroupReactionData(
                [
                    'group_id' => $groupId,
                    'users' => [
                        [
                            'user' => $userId
                        ]
                    ]
                ]
        );

        return GroupMembers::updateOrCreate(
                        [
                    'group_id' => $groupId,
                    'user_id' => $userId
                        ], [
                    'group_id' => $groupId,
                    'user_id' => $userId,
                    'is_admin' => 0,
                    'status' => 'P',
                        ]
        );
    }


    /**
     * Saves user's profile pic data in the database.
     *
     * @param array $data
     */
    public function saveGroupData($data) {
        // create conversion for group chat 
        return $this->createGroup($data);
    }

    /**
     * create group and save group data
     * 
     * @param type $userId
     * @param type $data
     * @return type
     */
    public function createGroup1($userId, $data) {
        $groupid = GroupRepository::saveFirebaseGroupData($userId, $data);
        $groupid = json_decode($groupid);
        $conversionResponse = UserRepository::saveFirebaseConversation('g', array($userId, $data['user_id'], $groupid->name));

        // send notification to device for group created 
        GroupRepository::groupCreateNotification($userId, $data, $groupid->name); //conversationId
        // save data in group table 
        $response = Groups::updateOrCreate(
                        [
                            'group_name' => $data['group_name'],
                            'group_icon' => $data['icon'],
                            'background_image' => $data['background'],
                            'conversion_id' => $conversionResponse['conversationId'],
                            'group_link' => GroupRepository::generatGroupLink($conversionResponse['conversationId']),
                        ]
        );

        // save data in group reaction table 
        GroupRepository::saveUserGroupReactionData(
                [
                    'group_id' => $response->id,
                    'users' => [
                        [
                            'user' => $userId,
                            'reaction' => '1'
                        ],
                        [
                            'user' => $data['user_id'],
                            'reaction' => '1'
                        ]
                    ]
                ]
        );

        // save data in group members table 
        $groupMembers = GroupRepository::saveUserMemberData(
                        [
                            'group_id' => $response->id,
                            'users' => [
                                [
                                    'user' => $userId,
                                    'admin' => '1'
                                ],
                                [
                                    'user' => $data['user_id'],
                                    'admin' => '0'
                                ]
                            ]
                        ]
        );

        return [
            'group_data' => $response,
            'group_members' => $groupMembers
        ];
    }

    public function createGroup($data) {

        $group = new Groups();  
        $group->status='A';
        $group->fill($data);
        $group->save();
        return $group;
    }

    public function updateGroupData($id, $conversationId) {

        return Groups::UpdateOrCreate(['id' => $id], ['conversion_id' => $conversationId]);
    }

    public function editGroupData($data, $conversationId) {

         $groups=Groups::where(['conversion_id' => $conversationId])->first();
 
         if($groups){
         $groups->fill($data);
         $groups->save();
         return $groups;
         }
         return false;
         
    }

    /**
     * update group data 
     * 
     * @param type $userId
     * @param type $data
     * @return type
     */
    public function updateGroup($userId, $data) {
        $groupid = GroupRepository::updateFirebaseGroupData($userId, $data);
        $dataArray = [];
        $dataArray['group_name'] = $data['group_name'];
        ((isset($data['icon']) && !empty($data['icon'])) ? ($dataArray['group_icon'] = $data['icon']) : "");
        ((isset($data['background']) && !empty($data['background'])) ? ($dataArray['background_image'] = $data['background']) : "");

        $response = Groups::updateOrCreate(
                        ['conversion_id' => $data['group_id']], $dataArray
        );

//        $groupMembers = GroupMembers::where([
//                    ['group_id', $response->id]
//                ])->get();
        $groupMembers = GroupRepository::getGroupMemberUsersData($response->id);

        return [
            'group_data' => $response,
            'group_members' => $groupMembers
        ];
    }

    /**
     * Generate Group Link for invitation
     * @param type $conversion_id
     * @return type
     */
    public function generatGroupLink($conversion_id) {
        return route('group-invite-link') . '/' . $conversion_id;
    }

    /**
     * Save User Member Data
     * @param type $users
     * @return boolean
     */
    public function saveUserMemberData($users) {

        $groupMembers = GroupMembers::insert($users);
    }

    /**
     * 
     * @param type $userid
     * @param type $data
     * @return type
     */
    public function saveFirebaseGroupData($userid, $data) {
        $firebase = new Firebase();
        $user_id = $data['user_id'];
// generate user array for firebase. 
        //  $convData = UserRepository::generateFirebaseConversionId([$userid, $user_id]);
        // list($firebaseUserid, $firebaseToUserid, $conversationId) = $convData;

        $firebaseArray = [
            'admin' => $firebaseUserid,
            'name' => $data['group_name'],
            'photo' => $data['icon'],
            'users' => [
                $firebaseUserid => "true",
                $firebaseToUserid => "true"
            ]
        ];
        // generate user array for firebase. 
//        $dataF = $firebase->get('/group/' . $firebasegroupid);
//        if (empty($dataF) || $dataF == null || $dataF == 'null') {
        if (isset($data['conversion_id']) && !empty($data['conversion_id'])) {
            $firebasegroupid = $data['conversion_id'];
            $response = $firebase->update('/group/', [$firebasegroupid => $firebaseArray]);
        } else {
            $response = $firebase->push('/group/', $firebaseArray);
        }
        return $response;
    }

    /**
     * Update firebase group data 
     * @param type $userid
     * @param type $data
     * @return type
     */
    public function updateFirebaseGroupData($userid, $data) {
        $firebase = new Firebase();
        $firebaseUId = $data['group_id'];

        $response = $firebase->update('/group/' . $firebaseUId . '/', ['name' => $data['group_name']]);
        if (isset($data['icon']) && !empty($data['icon'])) {
            $response = $firebase->update('/group/' . $firebaseUId . '/', ['photo' => $data['icon']]);
        }
        return $response;
    }

    /**
     * 
     * @param type $userId
     * @param type $data
     * @return type
     */
    public function saveResponseAction($userId, $data) {    // $userId: loged in user to check if user is admin for tghe group or not
//        if ($data['action'] == "A") {
//            $groupdata = GroupRepository::getGroupId($data['group_id']);
//            echo "<pre>";
//            print_r($groupdata);
//            exit;
//            $firebase = new Firebase();
//            $firebase->update('/group/' . $groupId . '/users', ['user_' . $data['user_id'] => true]);
////            $firebase->set('/group/' . $firebaseUserid . '/users', $firebaseArray);
//            // update in firebase data to add the users 
//        }
        $groupId = GroupRepository::getGroupByCovId($data['group_id']);
        return GroupMembers::where([
                    ['group_id', $groupId->id],
                    ['user_id', $data['user_id']]
                ])->update([
                    'status' => $data['action']
        ]);
    }

    /**
     * get Pending request 
     * 
     * @param type $group_id
     * @return type
     */
    public function getPendingRequests($group_id) {
//        DB::enableQueryLog();
        return GroupMembers::where([
                            ['groups.conversion_id', $group_id],
                            ['group_members.status', 'P']
                        ])
                        ->leftJoin('groups', 'group_members.group_id', '=', 'groups.id')
                        ->leftJoin('users', 'group_members.user_id', '=', 'users.id')
                        ->leftJoin('user_profile_pics', 'user_profile_pics.user_id', '=', 'users.id')
                        ->select('group_members.*', 'groups.conversion_id', 'users.first_name', 'users.last_name', 'users.email', 'user_profile_pics.storage_path', 'user_profile_pics.url')
                        ->groupBy('group_members.user_id')
                        ->get();
//        $data = DB::getQueryLog();
//        echo "<pre>";print_r($data);exit;
    }

    /**
     * change status to 'D' while user left the group
     * @param type $user_id
     * @param type $group_id
     * @return type
     */
    public function deleteGroupMember($user_id, $group_id) {
        // get group primary id 
        $groupId = $this->getGroupByCovId($group_id);
        return GroupMembers::where([
                            ['user_id', $user_id],
                            ['group_id', $groupId->id]
                        ])
                        ->update([
                            'status' => 'D'
        ]);
    }

      /**
     * change status to 'D' while user left the group
     * @param type $user_id
     * @param type $group_id
     * @return type
     */
    public function deleteGroupMemberById($userId, $groupId) {
        // get group primary id 

        return GroupMembers::where([
                            ['user_id', $userId],
                            ['group_id', $groupId]
                        ])
                        ->update([
                            'status' => 'D'
        ]);
    }
    /**
     * 
     * @param type $user_id
     * @param type $group_id
     * @return type
     */
    public function deleteGroup($user_id, $group_id) {
        // check if user is admin or not

        $adminUser = GroupMembers::where([
                    ['groups.conversion_id', $group_id],
                    ['group_members.user_id', $user_id],
                    ['group_members.is_admin', '1']
                ])
                ->leftJoin('groups', 'group_members.group_id', '=', 'groups.id')
                ->first();

        if ($adminUser) {    // delete group - only change the status of  the group
            Groups::where('conversion_id', $group_id)->update(['status' => 'D']);
            GroupMembers::where('group_id', $adminUser->id)->update(['status' => 'D']);
            return true;
        }
        return false;
    }

    /**
     * get group  details with the group members, name, icon
     * @param type $group_id
     * @return type
     */
    public function getGroupDetails($group_id) {
        $response = GroupRepository::getGroupByCovId($group_id);
        $groupMembers = GroupRepository::getGroupMemberUsersData($response->id);
        return [
            'group_data' => $response,
            'group_member' => $groupMembers
        ];
    }



  
    // list group
    public function listGroup($authId){

          $results =GroupMembers::
                   leftJoin('groups', 'group_members.group_id', '=', 'groups.id')
                  ->where('group_members.user_id', $authId)
                  ->where('group_members.status', 'A')
                  ->pluck('conversion_id', 'conversion_id')
                  ->toArray();
      
       return $results;
      
    }
    
    // edit group members
    public function editGroupMember($authUserId, $data,$groupData){
   
        $explode=explode(",",$data['user_id']);
        $createdMembers=[];
         $updatedMembers=[];
        // GroupMembers::where('group_id', $data['group_id'])->update(['status'=>'D']);
        foreach($explode as $userId){

        $groupMembers= GroupMembers::where(['user_id'=>$userId,'group_id'=>$groupData->id,'is_admin'=>0,'status'=>'A'])->first();

        if(count($groupMembers)>0){
             
            $updatedMembers[]=$groupMembers->id;
        }else{
        $groupMembers= GroupMembers::updateOrCreate(
                [ 'group_id'=>$groupData->id,'user_id'=>$userId],
                ['user_id'=>$userId,
                    'group_id'=>$groupData->id,
                    'is_admin'=>0,
                    'status'=>'A']
                );
     
        $updatedMembers[]=$groupMembers->id;
        $createdMembers[]=$groupMembers->user_id;
        }
        }
      
        $deletedMembers = GroupMembers::select('user_id')
                ->where('group_id', $groupData->id)
                 ->where('status','!=' ,'D')
                ->whereNotIn('id',$updatedMembers)
                ->get()
                ->toArray();

        return ['createdMembers'=>$createdMembers,'deletedMembers'=>$deletedMembers ];
    }


}
