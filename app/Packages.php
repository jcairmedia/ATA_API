<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    protected $fillable = [
        'name',
        'description',
        'periodicity',
        'amount',
        'id_plan_openpay',
        'state',
        'created_at',
        'updated_at',
    ];
}
