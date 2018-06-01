<?php

namespace App\Repositories;

use Twilio\Rest\Client as TwilioClient;

class TwilioRepository
{
    /**
     * Saves log of the SMS sent.
     *
     * @param array $data
     */
    public function smsVerificationCode($authUserId,$mobileNumber,$verificationCode) {

        // Generate and save verification code.
//        $verificationCode = (string) mt_rand(100000, 999999);
      
        // SMS verification code.
        $twilioClient = new TwilioClient(
                config('twilio.account_sid'), config('twilio.auth_token')
        );
        $message = $twilioClient->messages->create(
                // the number you'd like to send the message to
                $mobileNumber, [
            // A Twilio phone number you purchased at twilio.com/console
            'from' => config('twilio.from_phone_number'),
            // the body of the text message you'd like to send
//                'body' => $verificationCode
//            'body' => config('twilio.sms_body') . $verificationCode
            'body' => str_replace(["{verificationcode}", "{appname}"], [$verificationCode, config('app.name')], config('twilio.sms_body'))
                ]
        );
      
       return $message;
    }
    
    
    public function generateRandomNumberForVerification() {
        $verificationCode = (string) mt_rand(100000, 999999);
        return $verificationCode;
    }
}
