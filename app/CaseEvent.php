<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaseEvent extends Model
{
    protected $fillable = [
        'case_id',
        'subject',
        'description',
        'url_zoom',
        'date_start',
    ];
}
