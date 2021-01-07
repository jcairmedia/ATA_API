<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenpayCustomerCards extends Model
{
    protected $fillable = [
        'user_id',
        'id_card_open_pay',
        'card_number',
        'response',
        'active',
    ];
}
