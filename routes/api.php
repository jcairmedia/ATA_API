<?php

use App\Http\Controllers\API\AppointmentDateController;
use App\Http\Controllers\API\UsersController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/users', 'middleware' => []], function () {
    Route::post('/', [UsersController::class, 'register']);
    // Route::get('/:id', [UserController::class, 'byId']);
});
Route::get('/hours', [AppointmentDateController::class, 'hours']);
