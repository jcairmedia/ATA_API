<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarEventMeeting extends Model
{
    protected $fillable = [
       'meetings_id',
       'idevent',
       'idcalendar',
    ];
}
