<?php

namespace App\Main\Users\Domain;

use App\User;

class FindUserByIdDomain
{
    public function __invoke($array)
    {
        return User::where($array)->first();
    }
}
