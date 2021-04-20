<?php

use App\Http\Controllers\API\CRUDFirstPaymentOfflineContractPackageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Expone evidencia img or pdf

Route::get('/evidencefile', [CRUDFirstPaymentOfflineContractPackageController::class, 'getEvidence'])->name('evidencefile');

// Only test Form paid by target open pay
Route::get('/', function () {
    return view('formpaidonline');
});
