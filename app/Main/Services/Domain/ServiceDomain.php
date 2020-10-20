<?php

namespace App\Main\Services\Domain;

use App\Services;

class ServiceDomain
{
    public function __construct()
    {
    }

    public function all()
    {
        return Services::all();
    }
}
