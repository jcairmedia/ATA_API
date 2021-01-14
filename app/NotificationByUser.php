<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationByUser extends Model
{
    protected $fillable = [
        'title',
        'body',
        'user_id',
        'user_session_id',
    ];
}
