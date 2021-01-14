<?php

namespace App\Main\Users\Domain;

use App\User;

class SearchUsersByArrayEmailsDomain
{
    public function __invoke($arrayEmails)
    {
        return User::query()->whereIn('email', $arrayEmails)->select('id')->get()->pluck('id');
    }
}
