<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documents_case extends Model
{
    protected $fillable = [
        'folio',
        'reviewer_user_id',
        'case_id',
        'url',
        'status',
        'time_review',
        'number_times_review',
        'created_at',
        'updated_at',
    ];
}
