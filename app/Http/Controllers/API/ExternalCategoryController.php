<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\ExternalCategory\Domain\SelectCategoriesDomain;
use Illuminate\Http\Request;

class ExternalCategoryController extends Controller
{
    public function index(Request $request)
    {
        $ObjsCategories = (new SelectCategoriesDomain())();
        return response()->json($ObjsCategories->toArray(), 200);
    }
}
