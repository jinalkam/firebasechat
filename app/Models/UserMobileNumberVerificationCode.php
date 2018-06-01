<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMobileNumberVerificationCode extends Model
{
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
