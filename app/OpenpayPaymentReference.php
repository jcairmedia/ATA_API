<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenpayPaymentReference extends Model
{
    protected $fillable = [
        'meeting_id',
        'description',
        'error_message',
        'authorization',
        'amount',
        'operation_type',
        'payment_type',
        'payment_reference',
        'payment_barcode_url',
        'order_id',
        'transaction_type',
        'creation_date',
        'currency',
        'status',
        'method',
        'json_create_reference',
        'json_complete_refence',
    ];
}
