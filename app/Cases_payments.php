<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cases_payments extends Model
{
    protected $fillable = [
        'folio',
        'type_paid',
        'card_type',
        'bank',
        'currency',
        'brand',
        'bank_auth_code',
        'cases_id',
        'created_at',
        'updated_at',
    ];
}
