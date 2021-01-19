<?php

namespace App\Main\Groups\Domain;

use App\GroupUser;

class JoinUserByGroup
{
    public function __invoke($idGroup)
    {
        return GroupUser::where(['group_users.group_id' => $idGroup])
        ->join('users', 'users.id', '=', 'group_users.user_id')
        ->select(['group_users.*'])
       ->get();
    }
}
