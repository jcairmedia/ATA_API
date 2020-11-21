<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'start',
        'end',
        'active',
    ];
}
