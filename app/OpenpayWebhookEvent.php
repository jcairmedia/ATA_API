<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenpayWebhookEvent extends Model
{
    protected $fillable= [
        "type",
        "status",
        "hook_id",
        "order_id",
        "json",
    ];
}
