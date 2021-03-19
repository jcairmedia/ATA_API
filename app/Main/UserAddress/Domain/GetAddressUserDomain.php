<?php

namespace App\Main\UserAddress\Domain;

use App\UserAddress;

class GetAddressUserDomain
{
    public function __invoke(int $iduser)
    {
        return UserAddress::where(['users_id' => $iduser])->first();
    }
}
