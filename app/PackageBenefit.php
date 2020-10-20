<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageBenefit extends Model
{
    protected $fillable = [
        'package_id',
        'name',
    ];
}
