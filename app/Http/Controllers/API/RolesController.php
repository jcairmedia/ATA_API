<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use  App\Main\Roles\Domain\GetRolesDomain;
class RolesController extends Controller
{
    public function add(Request $request)
    {
        try {
            $rol = Role::where(['name' => $request->input('name')])->first();
            if ($rol != null) {
                throw new \Exception('Rol ya existe', 500);
            }
            $rol = new Role(['name' => $request->input('name')]);
            $rol->save();

            return response()->json(['code' => 201, 'message' => 'Rol creado', 'data' => []]);
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

    public function associate(Request $request)
    {
        try {
            $permission = $request->input('permission');
            $role = $request->input('role');
            $rolObj = Role::where(['name' => $role])->first();
            if ($rolObj == null) {
                throw new \Exception('Rol no existe', 500);
            }
            $permisoObj = Permission::where(['name' => $permission])->first();
            if ($permisoObj == null) {
                throw new \Exception('Permiso no existe', 500);
            }
            $rolObj->givePermissionTo($permisoObj);

            return response()->json(['code' => 201, 'message' => 'Permiso asociado al rol', 'data' => []]);
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

    public function list()
    {
        try{
            $roles = (new GetRolesDomain())();
            return response()->json(
                ['data' => $roles->toArray()], 200
            );
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
