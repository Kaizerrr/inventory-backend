<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\Api\Auth\SigninController;
use App\Http\Controllers\Api\Auth\SignoutController;
use App\Http\Controllers\Api\Auth\SignupController;
use App\Http\Controllers\StudentRecordController;
use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/example', function () {
    return response()->json(['message' => 'Hello, world!']);
});

// Route::middleware('json.response')->group(function () {
Route::apiResource('auth/signup', SignupController::class);
Route::apiResource('auth/signin', SigninController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('auth/signout', SignoutController::class);
    Route::get('/requirements', 'App\Http\Controllers\RequirementController@index');
    Route::get('/programs', 'App\Http\Controllers\Api\ProgramController@index');
    Route::get('/records', 'App\Http\Controllers\StudentRecordController@index');
    Route::apiResource('/students', StudentController::class);
    Route::apiResource('/user', UserController::class);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::get('/exportAll', [StudentController::class, 'exportAll']);


    Route::post('/upload', 'App\Http\Controllers\UploadController@upload');
    Route::get('/download/{filename}', 'App\Http\Controllers\UploadController@download');
    Route::delete('/delete-file/{filename}', 'App\Http\Controllers\UploadController@delete');

    Route::delete('/delete/{id}', [StudentController::class, 'delete']);

    Route::put('/update/{id}', [StudentController::class, 'update']);

    Route::post('/import', [StudentController::class, 'storeMultiple']);

    Route::get('/export/{id}', [StudentController::class, 'export']);

    Route::get('/records/all', [StudentController::class, 'getAll']);


    ////////////////////////////////////////////////////////////

    Route::post('/items', 'ItemsController@store');
});;
