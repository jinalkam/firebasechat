<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\UserDevice;
use Carbon\Carbon;
use NotificationsHelper;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
     public $fullname;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'facebook_id',
        'first_name',
        'last_name',
        'gender',
        'description',
        'blur_pics_before_matching',
        'gender_preference',
        'email',
        'password',
        'dob',
        'facebook_verified',
        'linkedin_verified',
        'twitter_verified',
        'instagram_verified',
        'gplus_verified',
        'mobile_verified',
        'linkedin_id',
        'twitter_id',
        'instagram_id',
        'gplus_id',
        'mobile_number',
        'trust_level',
        'status',
        'block',
        'password',
        'timezone',
      
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Gets all the devices for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices() {
        return $this->hasMany(UserDevice::class);
    }

    public function setDobAttribute($value) {

      $this->attributes['dob'] = Carbon::parse($value)->format('Y-m-d');
    }
    
      /**
     * Gets all the profile pics for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profilePics() {
        return $this->hasMany(UserProfilePic::class);
    }
   /**
     * Gets first and last name
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    
    public function getFullname()
    {
        $firstName=$this->first_name ??'';
        $lastName=$this->last_name ?? '';
        return $firstName.' '.$lastName;
    }
  

}
