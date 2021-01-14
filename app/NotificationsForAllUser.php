<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationsForAllUser extends Model
{
    protected $fillable = [
        'title',
        'body',
        'user_session_id',
    ];
}
