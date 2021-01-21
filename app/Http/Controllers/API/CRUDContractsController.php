<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Contracts\Domain\PaginateContractsDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CRUDContractsController extends Controller
{
    public function paginate(Request $request)
    {
        $index = (int) $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 100;
        $clientId = $request->input('clientId') ?? 0;
        $array = [];
        if ($clientId > 0) {
            $array['clientId'] = $clientId;
        }
        $r = (new PaginateContractsDomain())($filter, $index, $byPage, $array);

        return response()->json($r);
    }

    public function seeContracts(int $id)
    {
        try {
            $modelCase = \App\Cases::where(['id' => $id])->first();
            if (is_null($modelCase)) {
                throw new \Exception('No existe contracto.', 404);
            }
            $filePath = storage_path('contracts'.DIRECTORY_SEPARATOR.$modelCase->url_doc);

            if (!File::exists($filePath)) {
                throw new \Exception('File not exists', 404);
            }
            $contents = File::get($filePath);
            $string = base64_encode($contents);

            return response()->json(['_base64_' => $string]);
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
