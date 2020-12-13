<?php

namespace App\Main\Roles\Domain;

use Spatie\Permission\Models\Role;

class GetRolesDomain
{
    public function __construct()
    {
    }

    public function __invoke()
    {
        try {
            return Role::all();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
