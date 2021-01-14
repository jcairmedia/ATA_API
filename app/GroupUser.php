<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $fillable = [
            'group_id',
            'user_id',
            'active',
            'user_session_id',
    ];
}
