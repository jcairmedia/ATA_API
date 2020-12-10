<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTestQuestionarieAnswer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'customer_tests_id',
        'question_id',
        'answer_id',
        'question',
        'answer',
        'active',
    ];
}
