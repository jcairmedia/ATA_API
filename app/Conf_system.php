<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conf_system extends Model
{
    protected $fillable = [
        'name',
        'description',
        'value',
        'created_at',
        'updated_at',
    ];
}
