<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Notifications\FireBaseNotificaton;
use App\Repositories\UserDeviceRepository;
use App\Repositories\UserRepository;
use App\Repositories\SettingsRepository;
use FCM;

class NotificationsHelper {

  
    use Notifiable;
    

    private $UserDeviceRepository;
    private $userRepository;
    private $settingsRepository;
    
    public function __construct(UserDeviceRepository $UserDeviceRepository,
            UserRepository $UserRepository,
            SettingsRepository $settingsRepository ) {
       $this->userDeviceRepository=$UserDeviceRepository;
       $this->userRepository=$UserRepository;
       $this->settingsRepository=$settingsRepository;
      
    }

    public function sendNotifications($notifiable,$data) {
 
       $notificationSettings=$this->settingsRepository->getNotifcationStatus($notifiable->id,3);
        
        if($notificationSettings == 1){ 
        $notification=  new FireBaseNotificaton($data);
        $dataReturn = $notification->toDatabase($notifiable);

        $returnvalue = $notifiable->routeNotificationFor('database')->create([
           //  'id' => $notification->id,
            'message' => $data['notification_message'],
            'from_id' => (empty(\Auth::user())) ? '' : \Auth::user()->id,
            'type' => $data['type'],
            'data' => $dataReturn,
            'read_at' => null,
            'is_read' => 0,
        ]);

        $response=self::sendNotification($returnvalue);
        return $response;
        }
    }
    
    
    public function sendNotification($returnvalue){
        
        $user = $this->userRepository->getById($returnvalue->notifiable_id);
       
         $unread = $user->unreadNotifications->count();
         $finaldata = array_merge($returnvalue->data, ['badge' => $unread]);
          
        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60 * 20);
        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setBadge($unread)
                ->setBody($returnvalue->data['message'])
                ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($finaldata);
        $option = $optionBuiler->build();
       
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // You must change it to get your tokens
        $tokens = $this->userDeviceRepository->getUserDeviceTokenById($returnvalue->notifiable_id);

        if (!empty($tokens)) {
            // You must change it to get your tokens
          $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
          $success=$downstreamResponse->numberSuccess();
         if($success>0){
            return  true;
         }
        return false;
        }
    }
  
}
