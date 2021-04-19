<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogEntry extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'external_category_id',
        'title',
        'description',
        'body',
        'url_img_main',
        'name_img_main',
        'status',
    ];
}
