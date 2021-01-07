<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenpayCustomer extends Model
{
    protected $fillable = [
        'id',
        'id_open_pay',
        'created_at',
        'updated_at',
    ];
}
