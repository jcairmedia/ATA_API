<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationByGroup extends Model
{
    protected $fillable = [
        'title',
        'body',
        'group_id',
        'user_session_id',
    ];
}
