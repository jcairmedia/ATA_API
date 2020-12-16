<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cases_payments extends Model
{
    protected $fillable = [
        'cases_id',
        'subscription_id',
        'folio',
        'amount',
        'type_paid',
        'card_type',
        'bank',
        'currency',
        'brand',
        'bank_auth_code',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'float',
    ];
}
