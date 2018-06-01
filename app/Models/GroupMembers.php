<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMembers extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'group_id',
        'user_id',
        'is_admin',
        'status',
    
      
    ];
    
   // public $timestamps = true;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
