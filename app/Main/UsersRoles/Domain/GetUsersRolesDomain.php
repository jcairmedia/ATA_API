<?php

namespace App\Main\UsersRoles\Domain;

use App\User;

class GetUsersRolesDomain
{
    public function __construct()
    {
    }

    public function __invoke()
    {
        try {
            $usuarios = User::with('roles')->get();

            return $usuarios;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
