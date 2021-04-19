<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];
}
