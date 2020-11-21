<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
    protected $fillable = [
        'start',
        'end',
        'active',
    ];
}
