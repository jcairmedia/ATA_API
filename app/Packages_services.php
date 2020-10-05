<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Packages_services extends Model
{
    protected $fillable = [
        'service_id',
        'package_id',
        'price',
        ];
}
