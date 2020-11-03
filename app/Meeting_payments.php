<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting_payments extends Model
{
    protected $fillable = [
        'price',
        'folio',
        'bank_auth_code',
        'type_payment',
        'card_type',
        'bank',
        'currency',
        'brand',
        'json',
        'meeting_id',
    ];
}
