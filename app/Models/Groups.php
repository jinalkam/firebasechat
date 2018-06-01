<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use URL;
class Groups extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'group_name',
        'group_icon',
        'background_image',
        'group_link',
        'conversion_id',
        'blocked',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
    
    public function getGroupIcon() {
        return (!empty($this->group_icon)) ?URL::to('/storage/app/uploads') . '/' . $this->group_icon : "";
    }
    
}
