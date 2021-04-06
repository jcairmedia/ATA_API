<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments_documents_case extends Model
{
    protected $fillable = [
        'documents_cases_id',
        'reviewer_user_id',
        'comment',
        'created_at',
        'updated_at',
    ];
}
