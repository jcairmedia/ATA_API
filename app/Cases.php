<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    // protected $fillable = [
    //     'packages_id',
    //     'url_doc',
    //     'price',
    //     'idfe',
    //     'id_customer_openpay',
    //     'created_at',
    //     'updated_at',
    //     'services_id',
    //     'users_id',
    //     'customer_id',
    //     'closed_at',
    // ];

    protected $fillable = [
    'folio',
    'packages_id',
    'url_doc',
    'price',
    'id_customer_openpay',
    'created_at',
    'updated_at',
    'services_id',
    'users_id',
    'customer_id',
    'closed_at',
    'state_paid_opening',
    'idfe',
];
    protected $table = 'cases';
}
