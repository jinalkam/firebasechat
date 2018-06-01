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

class UserController extends Controller {

    /**
     * The UserService object which provides different services (create, issue token etc.) to the UserController object.
     *
     * @var UserService
     */
    use AuthenticatesUsers;
    use ResponseTrait;

    private $userService;
    private $groupService;
    private $notification;

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

    /**
     * Get Server time 
     */
    public function getServerTime() {
        $now = Carbon::now();
        $timestamp = ($now->timestamp * config('constants.mili_to_micro_second'));
        $timesone = $now->timezone;
        $teimzonedata = array(
            "current_time" => (string) $now,
            "timestamp" => $timestamp,
            "timesone" => $timesone,
        );
        return $teimzonedata;
    }

    /**
     * Logs in User & issues application access token.
     *
     * @param Request $request
     * @return JSON
     */
    public function login(Request $request) {
        // Get request data.
        $data = $request->all();

        // Validate request data.
        $validator = $this->userService->validateCreateAccountData($data);
        if ($validator->fails()) {
            return $this->responseJson('error', $validator->errors()->first(), 400);
        }

        // Check if user with the same facebook id exists.
        $user = $this->userService->getByFacebookId($data['facebook_id'], $data);

        // create access token 
        $personalAccessTokenResult = $this->userService->issueAccessToken($user);
        // save device info
        $this->userService->saveDeviceInfo($user->id, $data['device_id'], $personalAccessTokenResult->token->getAttribute('id'));
        // function to save profile pic

        if ($request->file('profile_pic')) {
            // Save user's profile pic on 'uploads' storage disk using local driver.
            $storedFilePath = $this->userService->storeProfilePic($user->id, $request);
        }
        // get profile pics
        //$profile_pics = [];
        $profile_pics = $this->userService->getUserProfilePic($user->id);

        // transform response
        $data = (new UserTransformer)->transformLogin($user, $personalAccessTokenResult->accessToken, $profile_pics);
        return $this->responseJson('success', "logged in successfully", 200, $data);
    }

    public function createFirebaseConversion(Request $request) {
        // Get request data.
        $data = $request->all();
        // create firebase conversation
        $this->userService->updateConversation($this->authUserId, $data);
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
            return $this->responseJson('success', \config('constants.API_MESSAGE.SEND_SUCCESS_CONFIRM'), 200);
        } catch (\Exception $ex) {

            return $this->responseJson('error', $ex->getMessage(), 400);
        }
    }

    /**
     * list all conversation ID.
     *
     * @param Request $request
     * @return JSON
     */
    public function listConversation(Request $request) {
        try {
            // Get request data.
            $data = $request->all();
            // get user convesation listing
            $userListing = $this->userService->getConversationListing($this->authUserId);
            // get group conversation listing
            $groupListing = $this->groupService->getConversationListing($this->authUserId);
            // merge 2 array
            $combineArray = array_merge($userListing, $groupListing);

            // get firebase data array
            $firebaseArray = $this->userService->getFirebaseData($this->authUserId);

            // transform  listing data
            $data = (new UserTransformer)->transformListing($combineArray, $firebaseArray);

            return $this->responseJson('success', \config('constants.API_MESSAGE.CON_LIST'), 200, $data);
        } catch (\Exception $ex) {

            return $this->responseJson('error', $ex->getMessage(), 400);
        }
    }

    // set the settings of user on/off for notification
    public function settings(Request $request) {
        try {
            // Get request data.
            $data = $request->all();
            // get convesation listing
            $userListing = $this->userService->setNotification($this->authUserId, $data);
            return $this->responseJson('success', \config('constants.API_MESSAGE.SETTINGS_SUCCESS'), 200);
        } catch (\Exception $ex) {

            return $this->responseJson('error', $ex->getMessage(), 400);
        }
    }

    /**
     * Logs out user & revokes app access token.
     *
     * @param Request $request
     * @return JSON
     */
    public function logout(Request $request) {

        $this->userService->revokeAccessToken($request->user());
        return $this->responseJson('success', 'User logged out successfully.', 200);
    }

    public function profilePics(Request $request) {

        $data = $request->all();
        // Saving profile pics
        if (isset($data['profile_pics']) && !empty($data['profile_pics'])) {
            $this->userService->storeMultiProfilePics($this->authUserId, $data['profile_pics']);
        }

        $profile_pics = $this->userService->getUserProfilePics($this->authUserId);
        
        return $this->responseJson('success', \config('constants.API_MESSAGE.PROFILE_PICS_UPLOAD'), 200, $profile_pics);
    }

    public function getLastProfilePics(Request $request) {

        // get profile pics  
        $profile_pics = $this->userService->getProfilePics(10);
        if ($profile_pics) {
            return $this->responseJson('success', \config('constants.API_MESSAGE.PROFILE_PICS_LISTED'), 200, $profile_pics);
        }
        return $this->responseJson('success', \config('constants.API_MESSAGE.NO_RECORD'), 200);
    }

}
