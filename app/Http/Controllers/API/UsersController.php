<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;

class UsersController extends Controller
{
    public function register(UserRequest $req)
    {
        return $req->all();
        // code...
    }
}
