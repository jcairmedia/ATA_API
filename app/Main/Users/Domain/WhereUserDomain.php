<?php

namespace App\Main\Users\Domain;

use App\User;

class WhereUserDomain
{
    public function __invoke($arrayWhere)
    {
        return User::where($arrayWhere)->get();
    }
}
