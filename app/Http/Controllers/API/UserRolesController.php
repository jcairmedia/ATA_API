<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Main\UsersRoles\UseCases\GetUsersWithRolesUseCase;

class UserRolesController extends Controller
{
    public function list()
    {

        try{
            $data = (new GetUsersWithRolesUseCase())();
            return response()->json([
                'code' => 200,
                'data' => $data
            ], 200);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}