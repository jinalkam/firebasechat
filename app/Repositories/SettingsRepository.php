<?php

namespace App\Repositories;

use App\Models\Settings;
use App\Models\Settings_user;

class SettingsRepository
{
    /**
     * Saves log of the SMS sent.
     *
     * @param array $data
     */
    public function getValue() {

             return Settings::get();
    }
    
     /**
     * Saves log of the SMS sent.
     *
     * @param array $data
     */
    public function update($data) {
        if(isset($data['one_to_one_verification'])){
        Settings::updateOrCreate(['id' =>2], ['status'=>1]);
        }else{
         Settings::updateOrCreate(['id' =>2], ['status'=>0]);
        }
    }
    
    public function setSettingsNotification($userId,$data){
        
      $isAdminSettings = $this->getValue();
      $settingsId=$isAdminSettings[2]->id;
       return   Settings_user::updateOrCreate(['user_id'=>$userId,'settings_id'=>$settingsId],
             ['user_id'=>$userId,'settings_id'=>$settingsId,'value'=>$data['in_appmessages']]);
    }
    
    public function getNotifcationStatus($userId,$settingId){
  
        $settings= Settings_user::select('value')
                ->where('user_id',$userId)
                ->where('settings_id',$settingId)
                ->first();
    
        if($settings){
            return $settings->value;
        }
        return 1;
    }
}
