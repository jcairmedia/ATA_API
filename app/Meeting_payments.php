<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting_payments extends Model
{
    protected $fillable = [
        'price',
        'folio',
        'authorization_bank',
        'type_payment',
        'type_target',
        'bank',
        'currency',
        'brand',
        'payment_gateway',
        'meeting_id',
    ];
}
