<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'folio',
        'category',
        'type_meeting',
        'url_meeting',
        'dt_start',
        'dt_end',
        'price',
        'state_record',
        'state_paid',
        'dt_cancellation',
        'contacts_id',
        'users_id',
    ];
}
