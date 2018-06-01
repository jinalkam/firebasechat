<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContacts extends Model
{ 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'contact_user_id',
        'conversation_id',
        'created_at',
        'is_confirmed',
        'updated_at',
        'status'
    ];

    
}
