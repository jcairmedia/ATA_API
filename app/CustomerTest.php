<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTest extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'uuid',
        'questionnaire_id',
        'meeting_id',
        'active',
        'answered',
    ];
}
