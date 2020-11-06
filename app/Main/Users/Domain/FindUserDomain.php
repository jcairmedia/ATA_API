<?php

namespace App\Main\Users\Domain;

use App\User;

class FindUserDomain
{
    public function __invoke($code)
    {
        return User::where('confirmation_code', $code)->first();
    }
}
