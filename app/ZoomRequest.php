<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoomRequest extends Model
{
    protected $fillable = [
        'meeting_id',
        'join_url',
        'password',
        'start_time',
        'timezone',
        'idmeetingzoom',
        'json',
        'state_request',
    ];
}
