<?php

namespace App\Main\UsersRoles\UseCases;

use App\Main\UsersRoles\Domain\GetUsersRolesDomain;

class GetUsersWithRolesUseCase
{
    public function __construct()
    {
    }

    public function __invoke()
    {
        try {
            $usersCollection = (new GetUsersRolesDomain())();
            $col = $usersCollection->filter(function($v, $k){
                return count($v->roles) >0;
            });

            return array_values($col->toArray());
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
