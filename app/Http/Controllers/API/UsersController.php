<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Main\Users\Domain\UserCreatorDomain;
use App\Main\Users\UseCases\RegisterUseCase;

class UsersController extends Controller
{
    public function register(UserRequest $req)
    {
        try {
            //code...

            $r = new RegisterUseCase(new UserCreatorDomain());
            $r($req->all());

            return response()->json([
                'code' => 201,
                'message' => '',
                'data' => [],
            ], 201);
        } catch (\Exception $ex) {
            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }
}
