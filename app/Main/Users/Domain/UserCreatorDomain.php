<?php

namespace App\Main\Users\Domain;

use App\User;

class UserCreatorDomain
{
    public function __construct()
    {
    }

    public function __invoke(User $user)
    {
        try {
            $user->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
