<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'cases_id',
        'id_card_openpay',
        'id_suscription_openpay',
        'id_customer_openpay',
        'active',
        'dt_cancelation',
    ];
}
