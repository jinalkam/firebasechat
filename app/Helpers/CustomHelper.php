<?php

namespace App\Helpers;

use App\Repositories\UserRepository;
use App\Repositories\UserDeviceRepository;

class CustomHelper {

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

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userDeviceRepository = new UserDeviceRepository();
    }

    /**
     * Send notification message to  the devices 
     * 
     * @param type $notificationArray
     * @return type
     */
    public function send_notifications($notificationArray) {
        $senderId = 'user_' . $notificationArray['user_id'];
        // get device list and tokens
        $deviceData = $this->userDeviceRepository->getUserDeviceList($notificationArray['to_user_id']);
        $device_id = $this->formatDeviceTokenData($deviceData);
        // get device list and tokens
        // get user details for passing data for notification
        $userDetails = $this->userRepository->getById($notificationArray['user_id']);
        // get user details for passing data for notification

        $senderName = $userDetails['first_name'] . ' ' . $userDetails['last_name'];
//        $notification_title = str_replace_array('?', ['8:30', '9:00'], $notificationArray['notification_title']); // replace with array content
        $notification_message = str_replace_array('{senderName}', [$senderName], $notificationArray['notification_message']);
        $notification_title = $notificationArray['notification_title'];
        $notification_type = $notificationArray['notification_type'];

        $dataDetails = array(
            "conv_id" => $notificationArray['conversion_id'],
//            "msgid" => "messageUId",
            "senderId" => $senderId,
            "isGroup" => true,
            "title" => $notification_title,
            "message" => $notification_message,
            "type" => $notification_type
        );
        $notificationDetails = array(
            "id" => $notificationArray['conversion_id'],
//            "msgid" => "messageUId",
            "title" => $notification_title,
            "body" => $notification_message,
            "sound" => 'default',
//            "vibrate" => '0',
            "badge" => '1',
            "priority" => "high",
            "type" => $notification_type
//            "icon" => "myicon",
//            "largeIcon" => "large_icon",
//            "smallIcon" => "small_icon",
        );
        $notification_data['data'] = $dataDetails;
        $notification_data['notification'] = $notificationDetails;
        return $this->send_push_notificaiton($device_id, $notification_data);
    }
    
    /**
     * get array of device_id
     * 
     * @param type $deviceDetails
     * @return type
     */
    public function formatDeviceTokenData($deviceDetails) {
        $returnToken = [];
        foreach ($deviceDetails as $key => $val) {
            $returnToken[] = $val->device_id;
        }
        return $returnToken;
    }

    /**
     * Send Push Notification to the device
     * 
     * @param type $deviceid
     * @param type $message
     * @param type $details
     * @return type
     */
    public function send_push_notificaiton($deviceid = '', $message = '', $details = '') {
//        echo "<pre>";
//        print_r($message);
//        exit;
        $notificationData = $message['data'];
        $notification = $message['notification'];
//        exit;
        if (is_array($deviceid)) {
            $registration_ids = $deviceid;
        } else {
            $registration_ids = array($deviceid);
        }
        $fields = array(
            'registration_ids' => $registration_ids,
//            'data' => array('title' => 'Notification from OyeDeals', 'message' => $message),
            'data' => $notificationData,
//            'notification' => array('title' => 'Notification from OyeDeals', 'body' => $message),
            'notification' => $notification,
            "content_available" => true,
            "priority" => "high"
        );
//        echo '<pre>';print_r($fields);exit;
        $headers = array(
            'Authorization: key=AIzaSyDsEsEXFTcupd9qR0qGpvHgrJY9RQrelDE', // FIREBASE_API_KEY_FOR_ANDROID_NOTIFICATION
            'Content-Type: application/json'
        );
// Open connection
        $ch = curl_init();
// Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
// Execute post
        $result = curl_exec($ch);
        if ($result === false) {
            die('Curl failed:' . curl_errno($ch));
        }
// Close connection
        curl_close($ch);
//    print_r($result);
//    die;
        return $result;
    }
    
    
    public function checkRealisticEmailAddress($email, $record = 'MX') {
        list($user, $domain) = explode('@', $email);
        return checkdnsrr($domain, $record);
    }

}
