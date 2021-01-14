<?php

namespace App\Main\Groups\Domain;

use App\Group;

class CreateGroupDomain
{
    public function __invoke(Group $group)
    {
        try {
            $group->saveOrFail();

            return $group;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }
}
