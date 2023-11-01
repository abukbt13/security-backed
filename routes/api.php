<?php

use App\Http\Controllers\OffenceController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middlew
are group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/register',[UsersController::class,'register']);
Route::post('auth/login',[UsersController::class,'login']);
Route::get('auth/show-all',[UsersController::class,'show']);
Route::post('case/create',[UsersController::class,'create']);

Route::post('case/create',[OffenceController::class,'create']);
Route::get('case/show',[OffenceController::class,'show']);
Route::get('case/single/{id}/{secret}',[OffenceController::class,'show_single']);
//Route::group(['middleware' => 'auth:sanctum'], function () {
//    Route::post('profile/update', [ProfileController::class, 'update']);
//
//    Route::group(['middleware' => 'is_admin'], function () {
//        Route::post('files/update/{id}', [FilesController::class, 'update']);
//        Route::get('files/delete/{id}', [FilesController::class, 'delete']);
//    });
//});
