<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'folio',
        'category',
        'type_meeting',

        'idfe',
        
        'url_meeting',
        'dt_start',
        'dt_end',
        'price',
        'record_state',
        'paid_state',
        'dt_cancellation',
        'msg_cancellation',
        'contacts_id',
        'users_id',
        'dt_close',
        'dt_start_rescheduler',
        'dt_end_rescheduler',
        'notes',
        'lawyer'
    ];
    protected $casts = [
        'dt_start' => 'datetime:Y-m-d H:i:s',
        'dt_end' => 'datetime:Y-m-d H:i:s',
        'price' => 'decimal:2',
 ];
}
