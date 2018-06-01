<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\GroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use SafeStudio\Firebase\Firebase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Traits\ResponseTrait;
use App\Transformer\UserTransformer;
use App\Notifications\FireBaseNotificaton;
use NotificationsHelper;
use DB;

class GroupController extends Controller {

    /**
     * The UserService object which provides different services (create, issue token etc.) to the UserController object.
     *
     * @var UserService
     */
    use AuthenticatesUsers;
    use ResponseTrait;

    private $userService;
    private $notification;
    private $groupService;

    /**
     * Constructs the UserController object by injecting the required dependencies. Also protects the routes by setting the middleware.
     *
     * @param UserService $userService
     */
    public function __construct(
    UserService $userService, NotificationsHelper $notification, GroupService $groupService
    ) {
        // Protect routes by auth:api middleware. Put exceptions under 'except' key.
        $this->middleware('auth:api', [
            'except' => [
                'login',
            ]
        ]);
        // A middleware to set Authenticated user's ID.
        $this->middleware(function ($request, $next) {
            $this->authUserId = Auth::id();

            return $next($request);
        });

        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->notification = $notification;
    }

    // create firebase group
    public function createFirebaseGroup(Request $request) {
        DB::beginTransaction();
        try {
            // Get request data.
            $data = $request->all();
            // create group in firebase and save group data
            $groupData = $this->groupService->createGroup($this->authUserId, $data);
            // create firebase group conversation and data
            $this->groupService->createGroupMembers($this->authUserId, $groupData, $data);
            //   return $this->responseJson('error', \config('constants.API_MESSAGE.GROUP_NOT_CREATE'), 400);
        } catch (\Exception $ex) {
           
            DB::rollback();
            throw $ex;
            return $this->responseJson('error', $ex->getMessage(), 400);
        }
        DB::commit();
        return $this->responseJson('success', \config('constants.API_MESSAGE.GROUP_CREATE'), 200);
    }

    // edit firebase group
    public function editFirebaseGroup(Request $request) {
        DB::beginTransaction();
        try {
            // Get request data.
            $data = $request->all();
            // create group in firebase and save group data
            $groupData = $this->groupService->editGroup($this->authUserId, $data);
  
            if (isset($data['user_id'])) {
                // create firebase group conversation and data
                $this->groupService->updateGroupMembers($this->authUserId, $data,$groupData);
            }
            
           // return $this->responseJson('error', \config('constants.API_MESSAGE.GROUP_SOMETHING_WRONG') , 400);
        } catch (\Exception $ex) {
            DB::rollback();
            throw$ex;
            return $this->responseJson('error', $ex->getMessage(), 400);
        }
        DB::commit();
        return $this->responseJson('success', \config('constants.API_MESSAGE.GROUP_EDIT'), 200);
    }

    /**
     * send invitation.
     *
     * @param Request $request
     * @return JSON
     */
    public function sendInvitation(Request $request) {
        try {
            // Get request data.
            $data = $request->all();
            $senderId = isset($data['id']) ? $data['id'] : '';
            $fromUser = $this->userService->getUserById($this->authUserId);
            $toUser = $this->userService->getUserById($senderId);
            $profilePics = $this->userService->getUserProfilePic($fromUser->id);
            if ($toUser) {
                // send notification  method pass message,sender image,from user
                $fcmResponse = $this->notification->sendNotifications($toUser, [
                    'type' => 'sendonetoone',
                    'notification_message' => \config('constants.NOTIFICATION_MESSAGE.SEND_NOTIFI_CHAT'),
                    'image' => $profilePics,
                    'from_user' => $fromUser,
                ]);

                return $this->responseJson('success', \config('constants.API_MESSAGE.SEND_SUCCESS_CHAT'), 200);
            }
        } catch (\Exception $ex) {
            //   throw $ex;
            return $this->responseJson('error', $ex->getMessage(), 400);
        }
    }

    /**
     * confirm invitation.
     *
     * @param Request $request
     * @return JSON
     */
    public function confirmInvite(Request $request) {
        try {
            // Get request data.
            $data = $request->all();

            // Validate request data.
            $validator = $this->userService->validateInviteAcceptData($data);
            if ($validator->fails()) {
                return $this->responseJson('error', $validator->errors()->first(), 400);
            }
            // create firebase conversation
            $this->userService->updateConversation($data);
            return $this->responseJson('success', \config('constants.API_MESSAGE.SEND_SUCCESS_CHAT'), 200);
        } catch (\Exception $ex) {

            return $this->responseJson('error', $ex->getMessage(), 400);
        }
    }

    /**
     * delete group.
     *
     * @param Request $request
     * @return JSON
     */
    public function deleteFirebaseGroup(Request $request) {
        try {
            // Get request data.
            $data = $request->all();
            $response = $this->groupService->deleteGroup($this->authUserId, $data['group_id']);
            if ($response) {
                return $this->responseJson('success', \config('constants.API_MESSAGE.GROUP_DELETE'), 200);
            }
            return $this->responseJson('error', \config('constants.API_MESSAGE.GROUP_SOMETHING_WRONG'), 400);
        } catch (\Exception $ex) {
            return $this->responseJson('error', $ex->getMessage(), 400);
        }
    }

    /**
     * leave group.
     *
     * @param Request $request
     * @return JSON
     */
    public function leaveFirebaseGroup(Request $request) {
        try {
            // Get request data.
            $data = $request->all();
            $response = $this->groupService->leaveGroup($this->authUserId, $data['group_id']);
            if ($response) {
                return $this->responseJson('success', \config('constants.API_MESSAGE.GROUP_DELETE'), 200);
            }
            return $this->responseJson('error', \config('constants.API_MESSAGE.GROUP_SOMETHING_WRONG'), 400);
        } catch (\Exception $ex) {
            return $this->responseJson('error', $ex->getMessage(), 400);
        }
    }

}
