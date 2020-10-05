<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'lastname_1',
        'lastname_2',
        'email',
        'phone',
        'created_at',
        'updated_at',
    ];
}
