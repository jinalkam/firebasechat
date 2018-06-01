<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MobileNumberService;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use App\Transformer\UserTransformer;

class MobileNumberController extends Controller {

    // use response trait
      use ResponseTrait;
    /**
     * The MobileNumberService object which provides different services (save, verify, resend verification code etc.) to the MobileNumberController object.
     *
     * @var MobileNumberService
     */
    private $mobileNumberService;

    /**
     * Stores the currently authenticated user's ID.
     *
     * @var integer
     */
    private $authUserId;

    /**
     * The UserService object which provides services for users
     * 
     * @var UserService
     */
    private $userService;

    
    
    /**
     * Constructs the MobileNumberController object by injecting the required dependencies. Also protects the routes by setting the middleware. Sets authenticated user's ID.
     *
     * @param MobileNumberService $mobileNumberService
     */
    public function __construct(
    MobileNumberService $mobileNumberService, UserService $userService
    ) {
        // Protect routes by auth:api middleware. Put exceptions under 'except' key.
        $this->middleware('auth:api');

        // A middleware to set Authenticated user's ID.
        $this->middleware(function ($request, $next) {
            $this->authUserId = Auth::id();

            return $next($request);
        });

        $this->mobileNumberService = $mobileNumberService;
        $this->userService = $userService;
    }

    /**
     * Saves mobile number. Sends verification code SMS to the saved mobile number.
     *
     * @param Request $request
     * @return JSON
     */
    public function save(Request $request) {
          
        try {
        // Get request data.
        $data = $request->all();

        // Validate request data.
        $validator = $this->mobileNumberService->validateMobileNumberData($data);
        if ($validator->fails()) {
          return $this->responseJson('error', $validator->errors()->first(), 400);
        
        }
         // check if mobile number already registered with other account or not!
        $response = $this->userService->checkVerifiedPhoneNumber($data['mobile_number']);
        if ($response) {
            return $this->responseJson('error', config('constants.API_MESSAGE.MOBILE_UNIQUE'), 400); 
        }
     
        // SMS verification code.
        $this->mobileNumberService->sendCode($this->authUserId, $data);
            
       // // update mobile number
        $this->mobileNumberService->update($this->authUserId, $data);

        } catch (\Exception $ex) {
        //   throw $ex;
            return $this->responseJson('error', $ex->getMessage(), 400);
         
        }
        
      return $this->responseJson('success', config('constants.API_MESSAGE.VERIFY_CODE_SUCCESS'), 200);
       
    }

    /**
     * Verifies user's mobile number if the submitted verification code is correct.
     *
     * @param Request $request
     * @return JSON
     */
    public function verify(Request $request) {
        try {
        // Get request data.
        $data = $request->all();

        // Check verification code.
        if (!$this->mobileNumberService->verificationPasses($this->authUserId, $data['verification_code'])) {
          
            return $this->responseJson('error', config('constants.API_MESSAGE.MOBILE_CODE_VERIFCATION_FAILED'), 400);
        }
       $profile_pic= $this->userService->getUserProfilePic($this->authUserId);
       $final_array = array_merge($data,['profile_pic'=>$profile_pic]);

        // Verification passed, so verify user's mobile number.
        $this->mobileNumberService->verify($this->authUserId, $final_array);

        }
         catch (\Exception $ex) {
           throw $ex;
            return $this->responseJson('error', $ex->getMessage(), 400);
         
        }
         return $this->responseJson('success', config('constants.API_MESSAGE.MOBILE_CODE_VERIFCATION_SUCCESS'), 200);
    }

    /**
     * Sends verification code SMS to the saved mobile number.
     *
     * @param Request $request
     * @return JSON
     */
    public function resendVerificationCode(Request $request) {
        try{
        // Get request data.
        $data = $request->all();

        // Validate request data.
        $validator = $this->mobileNumberService->validateMobileNumberData($data);
        if ($validator->fails()) {
          return $this->responseJson('error', $validator->errors()->first(), 400);
        
        }
        // SMS verification code.
        $this->mobileNumberService->sendCode($this->authUserId, $data);
          
        }
           catch (\Exception $ex) {
        //   throw $ex;
            return $this->responseJson('error', $ex->getMessage(), 400);
         
        }
         return $this->responseJson('success', config('constants.API_MESSAGE.VERIFY_CODE_SUCCESS'), 200);
    }
    
     /**
     * Sends verification code SMS to the saved mobile number.
     *
     * @param Request $request
     * @return JSON
     */
    public function getContactsLists(Request $request) {
        try{
        // Get request data.
        $data = $request->all();

        // Validate request data.
        $validator = $this->mobileNumberService->validateContactData($data);
        if ($validator->fails()) {
          return $this->responseJson('error', $validator->errors()->first(), 400);
        }
        // get user id which are in app contact
         $data=  $this->mobileNumberService->getContactUserList($this->authUserId,$data);
        
         //save the contacts in user contacts table
         $savedContact=$this->mobileNumberService->saveUserContacts($this->authUserId,$data);
         // create firebase conversation
         $this->userService->createConversation($this->authUserId,$savedContact);
        }
           catch (\Exception $ex) {
        //   throw $ex;
            return $this->responseJson('error', $ex->getMessage(), 400);
         
        }
        if(count($data)==0){
             return $this->responseJson('success', config('constants.API_MESSAGE.GET_NO_CONTACT'), 200);
        }
         //$data = (new UserTransformer)->transformContactList($data);
         return $this->responseJson('success', config('constants.API_MESSAGE.GET_CONTACT_SUCCESS'), 200,$data);
    }

}
