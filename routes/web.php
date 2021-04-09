<?php

use App\Http\Controllers\API\CRUDFirstPaymentOfflineContractPackageController;
use App\Utils\SendEmail;
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
// Expone evidencia
Route::get('/evidencefile', [CRUDFirstPaymentOfflineContractPackageController::class, 'getEvidence'])->name('evidencefile');

Route::get('/', function () {
    return view('formpaidonline');
});
Route::get('/contract', function () {
    $sendEmail = new SendEmail();
    $view = view('layout_contract_package');
    $sendEmail(['email' => 'noreply@usercenter.mx'], ['erika@airmedia.com.mx'], 'ATA| Confirmación de email', '', $view);

    return view('layout_contract_package');
});
