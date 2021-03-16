<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    protected $fillable = [
        'packages_id',
        'url_doc',
        'price',
        'idfe',
        'id_customer_openpay',
        'created_at',
        'updated_at',
        'services_id',
        'users_id',
        'customer_id',
        'closed_at',
    ];
    protected $table = 'cases';
}
