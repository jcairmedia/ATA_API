<?php

namespace App\Main\Groups\Domain;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateGroupUserBatchDomain
{
    public function __invoke($groupUser)
    {
        DB::table('group_users')->insert($groupUser);

        return $groupUser;
    }
}
