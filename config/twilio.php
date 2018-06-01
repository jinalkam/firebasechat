<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Twilio Account Credentials
      |--------------------------------------------------------------------------
      |
      | These credentials are required to use Twilio REST APIs.
      | Use developer account credentials in development environment.
      | Use client account credentials in production environment.
     */

    // Developer account credentials
    // A Twilio phone number you purchased at twilio.com/console
//    'from_phone_number' => '+18564855225',
//    'from_phone_number' => '+18442859779',
    'from_phone_number' => '+18452540838',
    // Account SID
    'account_sid' => 'AC4c9d7ce26c707fa84051f501530ff223',    // todd account
    // Auth Token

    'auth_token' => 'bbea844b819f36599a85b5e344b137de',
        // Client account credentials
        //'account_sid' => '',
        //'auth_token' => '',
    'sms_body' => 'Your {appname} verification code is {verificationcode}',
];
