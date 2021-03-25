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
        'users_id',
        'idcp',
        'street',
        'out_number',
        'int_number',
        'colonia',
        'created_at',
        'updated_at',
    ];
}
