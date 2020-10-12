<?php

namespace App\Main\Users\UseCases;

use App\Main\Users\Domain\UserCreatorDomain;
use App\User;

class RegisterUseCase
{
    public function __construct(UserCreatorDomain $userCreatorDomain)
    {
        $this->userCreatorDomain = $userCreatorDomain;
    }

    public function __invoke(array $user)
    {
        $userObj = new User($user);
        $userObj->password = \Hash::make($userObj->password);
        $this->userCreatorDomain->__invoke($userObj);
    }
}
