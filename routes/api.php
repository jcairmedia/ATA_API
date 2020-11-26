<?php

use App\Http\Controllers\API\AppointmentDateController;
use App\Http\Controllers\API\BenefitsController;
use App\Http\Controllers\API\CRUDMeetingController;
use App\Http\Controllers\API\FreeMeetingController;
use App\Http\Controllers\API\OfflinePaidMeetingController;
use App\Http\Controllers\API\OnlinePaidMeetingController;
use App\Http\Controllers\API\PackagesController;
use App\Http\Controllers\API\PaidMeetingController;
use App\Http\Controllers\API\ServicesController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\WebHookOfflinePaidMeetingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', [UsersController::class, 'getuser'])->middleware(['auth:api', 'verified']);

Route::group(['prefix' => '/users', 'middleware' => []], function () {
    Route::post('/', [UsersController::class, 'register']);
    // Route::get('/:id', [UserController::class, 'byId']);
});
Route::get('/hours', [AppointmentDateController::class, 'hours']);

// Beneficios
Route::get('/benefits', [BenefitsController::class, 'all']);
Route::get('/benefits/{idPackage}', [BenefitsController::class, 'byPackage'])->where('idPackage', '[0-9]+');
Route::get('/services', [ServicesController::class, 'all']);

Route::get('/packages', [PackagesController::class, 'all']);
Route::group(['prefix' => 'contracts', 'middleware' => 'auth:api'], function () {
    Route::post('', [PackagesController::class, 'contract']);
});

Route::post('/meeting/free', [FreeMeetingController::class, 'register']);
Route::post('/meeting/paid', [PaidMeetingController::class, 'register']);
Route::post('/meeting/paid/offline', [OfflinePaidMeetingController::class, 'index']);
Route::post('/meeting/paid/online', [OnlinePaidMeetingController::class, 'index']);

// valid code for activate user
Route::get('/register/verify/{code}', [UsersController::class, 'verify']);

// request in open pay
Route::post('/hook', [WebHookOfflinePaidMeetingController::class, 'index']);

Route::get('/meetings/list', [CRUDMeetingController::class, 'list']);

Route::post('/meeting/state', [CRUDMeetingController::class, 'updateStateMeeting']);

// Route::get('/hook', [WebHookOfflinePaidMeetingController::class, 'index']);
