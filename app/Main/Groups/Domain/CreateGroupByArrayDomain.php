<?php

namespace App\Main\Groups\Domain;

use App\Group;

class CreateGroupByArrayDomain
{
    public function __invoke(array $array)
    {
        try {
            $groupModel = Group::create($array);
            $groupModel->save();

            return $groupModel;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), (int) $ex->getCode());
        }
    }
}
