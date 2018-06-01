<?php

namespace App\Services;

use Carbon\Carbon;
use App\Repositories\UserRepository;
use App\Repositories\UserDeviceRepository;
use App\Repositories\TwilioRepository;
use App\Repositories\UserMobileNumberVerificationCodeRepository;
use App\Repositories\SmsLogRepository;
use App\Repositories\FirebaseRepository;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorInstance;
use URL;
use App\Repositories\SettingsRepository;
class MobileNumberService {

    /**
     * The UserDeviceRepository object to handle database operations.
     *
     * @var UserDeviceRepository
     */
    private $userDeviceRepository;

    /**
     * The UserMobileNumberVerificationCodeRepository object to handle database operations.
     *
     * @var UserMobileNumberVerificationCodeRepository
     */
    private $userMobileNumberVerificationCodeRepository;

    /**
     * The SmsLogRepository object to handle database operations.
     *
     * @var SmsLogRepository
     */
    private $smsLogRepository;
  /**
     * The SettingsRepository object to handle settings operations.
     *
     * @var SettingsRepository
     */
    private $settingsRepository;
    /**
     * Stores the current date time string.
     *
     * @var string
     */
    private $currentDateTime;
    private $userRepository;
    private $twilioRepository;

    /**
     * Constructs the MobileNumberService object by injecting the required dependencies.
     *
     * @param UserDeviceRepository $userDeviceRepository
     * @param UserMobileNumberVerificationCodeRepository $userMobileNumberVerificationCodeRepository
     * @param SmsLogRepository $smsLogRepository
     */
    public function __construct(
    UserDeviceRepository $userDeviceRepository,
            UserMobileNumberVerificationCodeRepository $userMobileNumberVerificationCodeRepository, 
            SmsLogRepository $smsLogRepository, UserRepository $userRepository, 
            TwilioRepository $twilioRepository, FirebaseRepository $firebase,
             SettingsRepository $settingsRepository
    ) {
        $this->userDeviceRepository = $userDeviceRepository;
        $this->userMobileNumberVerificationCodeRepository = $userMobileNumberVerificationCodeRepository;
        $this->smsLogRepository = $smsLogRepository;
        $this->userRepository = $userRepository;
        $this->twilioRepository = $twilioRepository;
        $this->firebaseRepository = $firebase;
        $this->settingsRepository = $settingsRepository;
        $this->currentDateTime = Carbon::now()->toDateTimeString();
    }

    /**
     * Validates mobile number data.
     *
     * @param array $data
     * @return ValidatorInstance
     */
    public function validateMobileNumberData(array $data): ValidatorInstance {

        return Validator::make($data, [
                    'mobile_number' => 'required|max:20',
                        ]
        );
    }

    /**
     * Validates mobile number data.
     *
     * @param array $data
     * @return ValidatorInstance
     */
    public function validateContactData(array $data): ValidatorInstance {

        return Validator::make($data, [
                    'contacts' => 'required',
                        ]
        );
    }

    /**
     * Saves user's mobile number.
     *
     * @param integer $authUserId
     * @param array $data
     */
    public function update($authUserId, $data) {
        $this->userRepository->save(
                $authUserId, $data
        );
    }

    /**
     * Checks if mobile number verification code is correct or incorrect.
     *
     * @param integer $authUserId
     * @param string $verificationCode
     * @return boolean Returns true if the verification code is correct, false otherwise.
     */
    public function verificationPasses($authUserId, $verificationCode) {
        return $this->userMobileNumberVerificationCodeRepository->match($authUserId, $verificationCode);
    }

    /**
     * Verifies user's mobile number.
     *
     * @param integer $authUserId
     * @param array $data
     */
    public function verify($authUserId, $data) {
        $verifyMobile = $this->userRepository->verifyMobileNumberUser($authUserId);
        if ($verifyMobile) {
            $users = $this->userRepository->setFirebaseUser($authUserId);
      
             $device=[];   
            if(isset($users->devices)){
          
                foreach($users->devices as $devices){
                   
                    $device[]=$devices->device_id;
                }
            }
          
            $firebaseArray = [
                        'id' => $users->id,
                        'last_seen' => (string) (Carbon::now()->timestamp * config('constants.mili_to_micro_second')),
                        'name' => $users->first_name . ' ' . $users->last_name,
                        'profile_pic' => $data['profile_pic'],
                        'token' => $device
            ];

            $this->firebaseRepository->createFireBaseUser($firebaseArray);
        }
    }

    public function generateRandomNumberForVerification() {
        $verificationCode = (string) mt_rand(100000, 999999);
        return $verificationCode;
    }

    /**
     * Generates and saves 6 digits verification code.
     * SMS verification code.
     * Saves log of sent SMS.
     *
     * @param integer $authUserId
     * @param array $data
     */
    public function sendCode($authUserId, $data) {

        $deviceId = $data['device_id'];
        $mobileNumber = $data['mobile_number'];
        $verificationCode = $this->generateRandomNumberForVerification();
        $this->userMobileNumberVerificationCodeRepository->save($authUserId, $verificationCode);
        $message = $this->twilioRepository->smsVerificationCode($authUserId, $mobileNumber, $verificationCode);

        // Save log of sent SMS.
        $this->smsLogRepository->create([
            'user_id' => $authUserId,
            'device_id' => $deviceId,
            'mobile_number' => $mobileNumber,
            'sms_id' => $message->sid,
            'sms_subject' => 'Mobile Number Verification Code',
            'sms_body' => str_replace(["{verificationcode}", "{appname}"], [$verificationCode, config('app.name')], config('twilio.sms_body'))
        ]);
    }

    public function getUserMobileNumberVerified($authUserId) {
        return $this->userRepository->getById($authUserId);
    }

    public function updateUserMobileVerification($authUserId, $data) {
        $this->userDeviceRepository->updateUserMobileVerification($authUserId, $data);
    }

    /**
     * dummy records for the mobile number while while signup with the mobile number 
     * to send OTP number , save the  dummy entry of the mobile data
     * save device_id, mobile_number, 
     * added on 23rd January 2018
     */
    public function saveMobiledata_dummy($data) {
        $response = $this->smsVerificationCode_dummy($data);
        $verification = $response->getData()->verification;
        return $this->userDeviceRepository->saveMobileNumber_dummy($data, $verification);
    }

    /**
     * Generates and saves 6 digits verification code.
     * SMS verification code.
     * Saves log of sent SMS.
     *
     * @param integer $authUserId
     * @param array $data
     */
    public function smsVerificationCode_dummy($data) {
        $deviceId = $data['device_id'];
        $mobileNumber = $data['mobile_number'];

        // Generate and save verification code.
        $verificationCode = $this->generateRandomNumberForVerification();
//        $this->userMobileNumberVerificationCodeRepository->save($authUserId, $verificationCode);
        // SMS verification code.
        $message = $this->sendTwilioMessage($mobileNumber, $verificationCode);

        return response()->json(
                        [
                            'verification' => $verificationCode,
                            'message' => $message
                        ]
        );

        // Save log of sent SMS.
//        $this->smsLogRepository->create([
//            'user_id' => $authUserId,
//            'device_id' => $deviceId,
//            'mobile_number' => $mobileNumber,
//            'sms_id' => $message->sid,
//            'sms_subject' => 'Mobile Number Verification Code',
//            'sms_body' => str_replace(["{verificationcode}", "{appname}"], [$verificationCode, "HelloLayover"], config('twilio.sms_body'))
//        ]);
    }

    public function sendTwilioMessage($mobile_number, $verification_code) {
        $twilioClient = new TwilioClient(
                config('twilio.account_sid'), config('twilio.auth_token')
        );
        return $twilioClient->messages->create(
                        // the number you'd like to send the message to
                        $mobile_number, [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => config('twilio.from_phone_number'),
                    // the body of the text message you'd like to send
//            'body' => config('twilio.sms_body') . $verificationCode
                    'body' => str_replace(["{verificationcode}", "{appname}"], [$verification_code, "HelloLayover"], config('twilio.sms_body'))
                        ]
        );
    }

    /**
     * Checks if mobile number verification code is correct or incorrect.
     *
     * @param integer $authUserId
     * @param string $verificationCode
     * @return boolean Returns true if the verification code is correct, false otherwise.
     */
    public function verificationPasses_dummy($device_id, $mobile_number, $verificationCode) {
        return $this->userDeviceRepository->match_update_dummy($device_id, $mobile_number, $verificationCode);
    }

    /**
     * save mobile no and generate list which are confirmed.
     *
     * @param integer $data
     * @return boolean Returns contact list.
     */
    public function getContactUserList($userId,$data) {
        $contacts = (json_decode($data['contacts']));
  
        return $this->userRepository->matchContact($userId,$contacts);
 
        
    }
    
     public function saveUserContacts($userId,$data) {
         $isAdminSettings = $this->settingsRepository->getValue();

         $confirmed= ($isAdminSettings[1]->status == 0)?1:0;        
         return $this->userRepository->saveContact($userId,$data,$confirmed);  

     }
    
    
    

}
