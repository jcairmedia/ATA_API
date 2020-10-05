<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cases_payments extends Model
{
    protected $fillable = [
        'folio',
        'type_paid',
        'type_target',
        'bank',
        'currency',
        'brand',
        'authorization',
        'cases_id',
        'created_at',
        'updated_at',
    ];
}
