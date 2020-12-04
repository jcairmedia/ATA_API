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
        'record_state',
        'state_paid',
        'dt_cancellation',
        'dt_close',
        'contacts_id',
        'users_id',
        'dt_start_rescheduler',
        'dt_end_rescheduler',

    ];
    protected $casts = [
        'dt_start' => 'datetime:Y-m-d H:i:s',
        'dt_end' => 'datetime:Y-m-d H:i:s',
        'price' => 'decimal:2',
 ];
}
