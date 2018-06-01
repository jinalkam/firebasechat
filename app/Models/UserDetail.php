<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use App\Http\Services\ImageTrait;
use App\Notifications\FcmNotification;
use Illuminate\Notifications\Notifiable;
use Notification;
use Carbon\Carbon;
use Auth;
class UserDetail extends Model {

  //  use ImageTrait;
    use Notifiable;

    protected $fillable = [
        'user_detail_id', 'first_name', 'last_name', 'dob', 'phone', 'profile_pic',
        'latitude', 'longitude', 'facebook_id', 'google_token', 'twitter_token', 'user_id',
        'category_id', 'type', 'notification_fav_expire', 'notification_new_offer',
        'notification_recieve_offer', 'gender'
    ];
    protected $table = 'user_detail';
    protected $dateFormat = 'Y-m-d';
    public $timestamps = false;

    const IS_NOT_CONFIRMED = 0;
    const IS_CONFIRMED = 1;

    protected $dates = [
        'dob',
    ];
    public $primaryKey = 'user_detail_id';

    public function setDobAttribute($value) {

        $this->attributes['dob'] = Carbon::parse($value)->format('Y-m-d');
    }

    public static function formatDob($value) {
        return Carbon::parse($value)->format('m/d/Y');
    }

}
