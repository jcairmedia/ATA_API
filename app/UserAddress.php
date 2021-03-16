<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'users_id';
    protected $fillable = [
        "idcp",
        "street",
        "out_number",
        "int_number",
        "created_at",
        "updated_at",
    ];
}
