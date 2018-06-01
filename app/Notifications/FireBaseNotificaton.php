<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications;

class FireBaseNotificaton extends Notification
{
    use Queueable;
    private $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
         $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
      return [Notifications::class];
    }

   

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
    
      /**
     * save the data to notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $message=$this->finalMessage();
     
        return [
                'type' => $this->data['type'],
                'message' => $message,
                'image' => empty($this->data['image']) ? '' : $this->data['image'],  
            ];
    }
    
    public function finalMessage(){
    
        $fromUserName=$this->data['from_user']->getFullname()??'';
        $find=['{{from_user_name}}'];
        $replace=[$fromUserName];
        $message = str_replace($find, $replace,$this->data['notification_message']);
        return $message;
        
    }
    
    
    
}
