<?php

return [
    /*
      |--------------------------------------------------------------------------
      | User Defined Variables
      |--------------------------------------------------------------------------
      |
      | This is a set of variables that are made specific to this application
      | that are better placed here rather than in .env file.
      | Use config('your_key') to get the values.
      |
     */
  'mili_to_micro_second' => env('MILI_TO_MICRO_SECOND', '1000'),
    // get messages 
    'API_MESSAGE' => [
        'MOBILE_UNIQUE' => 'Mobile number already verified with other account. Please use different account.',
        'MOBILE_REQUIRED'=>'Failed to save the mobile number. The request data is invalid. Check validation errors for more details.',
        'MOBILE_VALID_NUMBER'=>'Failed to send verification code SMS on this mobile number. Please make sure you entered a valid mobile number',
        'mobile_verification_code_sent' => 'Verification code SMS sent successfully.',
        'mobile_verification_code_sent_fail' => 'Failed to send verification code.',
        'user_not_found' => 'User not found.',
        'MOBILE_CODE_VERIFCATION_FAILED'=>'Mobile number verification failed. Submitted verification code is incorrect.',
        'MOBILE_CODE_VERIFCATION_SUCCESS'=>'Mobile number verified successfully.',
        'VERIFY_CODE_SUCCESS'=>'Verification code SMS sent successfully.',
        'GET_CONTACT_SUCCESS'=>'Contact Listed Successfully.',
        'GET_NO_CONTACT'=>'There Are No Contacts Matched.',
        'SEND_SUCCESS_CHAT'=>'Notification Sent Successfully.',
        'SEND_ERROR_CHAT'=>'Notification Not Sent Successfully.',
        'GROUP_CREATE'=>'Group Created Successfully.',
        'GROUP_NOT_CREATE'=>'GROUP Not Created Successfully.',
        'GROUP_DELETE'=>'Group Deleted Successfully.',
        'GROUP_SOMETHING_WRONG'=>'Something Went Wrong.',
        'GROUP_EDIT'=>'Group Edited Successfully.',
        'CON_LIST'=>'Conversation Listed Successfully.',
        'SEND_SUCCESS_CONFIRM'=>'Invitation Updated Successfully.',
        'SETTINGS_SUCCESS'=>'Settings Updated Successfully.',
        'NO_RECORD'=>'No Record Found.',
        'PROFILE_PICS_UPLOAD'=>'User profile pics uploaded successfully.',
        'PROFILE_PICS_LISTED'=>'User profile pics listed successfully'
    ],
    'NOTIFICATION_MESSAGE'=>[
        'SEND_NOTIFI_CHAT'=>'{{from_user_name}} has sent you a invitation for chat',
        'USER_ACCEPT'=> '{{from_user_name}} has accepted your invitation for chat',
        'USER_REJECT'=> '{{from_user_name}} has rejected your invitation for chat',
        'SEND_NOTIFI_CHAT_GROUP'=>'{{from_user_name}} has sent you a invitation for group chat',
        'SEND_NOTIFI_DELETE_GROUP_MEMBERS'=>'admin has removed you from a group'
        
    ]
];
