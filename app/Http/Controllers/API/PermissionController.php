<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function add(Request $request)
    {
        try {
            $permiso = Permission::where(['name' => $request->input('name')])->first();
            if ($permiso != null) {
                throw new \Exception('Permiso ya existe', 500);
            }
            $rol = new Permission(['name' => $request->input('name')]);
            $rol->save();

            return response()->json(['code' => 201, 'message' => 'Permiso creado', 'data' => []]);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}
