<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarEventMeeting extends Model
{
    protected $fillable = [
        'id',
       'meetings_id',
       'idevent',
       'idcalendar',
    ];
}
