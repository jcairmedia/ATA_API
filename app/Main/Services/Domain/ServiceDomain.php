<?php

namespace App\Main\Services\Domain;

use App\Services;

class ServiceDomain
{
    public function __construct()
    {
    }

    public function all(array $array_columns = null)
    {
        return ['data' => is_null($array_columns) || count($array_columns) <= 0 ? Services::all() : Services::all($array_columns)];
    }
}
