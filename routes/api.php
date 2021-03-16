<?php

use App\Http\Controllers\API\AppointmentDateController;
use App\Http\Controllers\API\BenefitsController;
use App\Http\Controllers\API\CasesController;
use App\Http\Controllers\API\CasesPaymentsController;
use App\Http\Controllers\API\CRUDFederativeEntitieController;
use App\Http\Controllers\API\CRUDMeetingController;
use App\Http\Controllers\API\CRUDPostalCodeController;
use App\Http\Controllers\API\CRUDQuestionController;
use App\Http\Controllers\API\CRUDQuestionnaireController;
use App\Http\Controllers\API\CRUDUserController;
use App\Http\Controllers\API\FreeMeetingController;
use App\Http\Controllers\API\MeetingReSchedulerController;
use App\Http\Controllers\API\OfflinePaidMeetingController;
use App\Http\Controllers\API\OnlinePaidMeetingController;
use App\Http\Controllers\API\PackagesController;
use App\Http\Controllers\API\PaidMeetingController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RCasesController;
use App\Http\Controllers\API\RolesController;
use App\Http\Controllers\API\ServicesController;
use App\Http\Controllers\API\TestCustomerController;
use App\Http\Controllers\API\UserRolesController;
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

Route::get('/user', [UsersController::class, 'getuser'])->middleware(['auth:api']);

Route::group(['prefix' => '/users', 'middleware' => []], function () {
    Route::post('/', [UsersController::class, 'register']);
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
// Route::post('/meeting/paid', [PaidMeetingController::class, 'register']);
Route::post('/meeting/paid/offline', [OfflinePaidMeetingController::class, 'index']);
Route::post('/meeting/paid/online', [OnlinePaidMeetingController::class, 'index']);

// valid code for activate user
Route::get('/register/verify/{code}', [UsersController::class, 'verify']);

// get federal entities
Route::get('/federalentities', [CRUDFederativeEntitieController::class, 'index']);
Route::get('/cp', [CRUDPostalCodeController::class, 'index']);
Route::get('/cp/asentas', [CRUDPostalCodeController::class, 'asentas']);

// request in open pay
Route::post('/hook', [WebHookOfflinePaidMeetingController::class, 'index']);

// Customer  Satisfaction - Questionnaire
Route::group(['prefix' => '/customersatisfaction'], function () {
    Route::get('/test', [TestCustomerController::class, 'getQuestions']);
    Route::post('/test', [TestCustomerController::class, 'saveTest']);
});

Route::post('/roles/add', [RolesController::class, 'add']);
Route::post('/permission', [PermissionController::class, 'add']);
Route::post('/roles/permission', [RolesController::class, 'associate']);

Route::post('/user/recover', [CRUDUserController::class, 'recoverPassword']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/questions', [CRUDQuestionController::class, 'list']);
    Route::get('/questionnaire/questions', [CRUDQuestionController::class, 'getQuestionsByQuestionnaireId']);
    Route::post('/question', [CRUDQuestionController::class, 'updateQuestion']);
    Route::get('/questionnaires', [CRUDQuestionnaireController::class, 'list']);
    Route::get('/users/rol', [UsersController::class, 'getUserByRol']);
    Route::post('/user/dash', [UsersController::class, 'registerUserDash']);
    Route::get('/roles', [RolesController::class, 'list']);
    Route::get('/users/roles', [UserRolesController::class, 'list']);
    Route::get('/meetings/list', [CRUDMeetingController::class, 'list']);
    Route::get('/meeting/questionnaire', [CRUDMeetingController::class, 'getQuestionnaireByMeetingId']);
    Route::post('/meeting/state', [CRUDMeetingController::class, 'updateStateMeeting']);
    Route::post('/meeting/rescheduler', [MeetingReSchedulerController::class, 'index']);

    Route::post('/user/update', [CRUDUserController::class, 'index']);
    Route::post('/user/pass', [CRUDUserController::class, 'resetPassword']);

    Route::post('/user/rol', [UsersController::class, 'associate_rol']);
    Route::get('/cases', [RCasesController::class, 'list']);
    Route::post('/case/lawyer', [RCasesController::class, 'setLawyer']);
    Route::delete('/cases', [CasesController::class, 'close']);
    Route::get('/cases/payments', [CasesPaymentsController::class, 'paymentsCase']);
    Route::post('/meeting/note', [CRUDMeetingController::class, 'setNote']);
});
