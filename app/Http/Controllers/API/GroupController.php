<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Main\Groups\Domain\CreateGroupByArrayDomain;
// use Illuminate\Http\Request;
use App\Main\Groups\Domain\CreateGroupUserBatchDomain;
use App\Main\Users\Domain\SearchUsersByArrayEmailsDomain;
use  App\Utils\LoadCSV;

class GroupController extends Controller
{
    public function index(GroupRequest $request)
    {
        try {
            $user = $request->user();
            $nameGroup = $request->input('group');
            if (!$request->file('file')->isValid()) {
                throw new \Exception('Error en la carga del archivo: '.$req->file('archivo'), 400);
            }
            $namefile = $request->file('file')->getClientOriginalName();
            $extension = pathinfo($namefile, PATHINFO_EXTENSION); //get extension
            if ($extension != 'csv') {
                throw new \Exception('Formato del archivo no válido', 400);
            }
            $worksheetCollection = (new LoadCSV())($request);
            $r = $worksheetCollection->first()->pluck(0)->filter(function ($v, $k) {
                return !empty($v) && filter_var($v, FILTER_VALIDATE_EMAIL);
            })->unique()->values()->toArray();

            if (count($r) <= 0) {
                return \response()->json(['message' => 'No hay datos válidos para importar'], 400);
            }
            // Search users
            $usersIdCollection = (new SearchUsersByArrayEmailsDomain())($r);
            if ($usersIdCollection->Count() <= 0) {
                throw new \Exception('Correos no validos', 404);
            }
            $groupModel = (new CreateGroupByArrayDomain())(['name' => $nameGroup, 'user_id' => $user->id]);
            // Create struct "GroupUser" for save
            $groupUser = $usersIdCollection->map(function ($v, $k) use ($groupModel, $user) {
                return [
                    'user_session_id' => $user->id,
                    'group_id' => $groupModel->id,
                    'user_id' => $v,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(), ];
            })->toArray();
            // Batch Save GroupUser
            $groupUser = (new CreateGroupUserBatchDomain())($groupUser);

            return response()->json([
                'message' => 'Grupo creado exitosamente',
            ], 200);

            // return $groupUser;
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
