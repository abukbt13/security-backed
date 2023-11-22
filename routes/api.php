<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourtCaseController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\OffenceController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VideoController;
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

Route::post('court/edit/{id}',[CourtCaseController::class,'edit']);
Route::post('court/status/{id}',[CourtCaseController::class,'change_status']);




//protect the route from unauthorised user
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request)
    {return $request->user();});


    Route::get('case/single/{id}/{secret}',[OffenceController::class,'show_single']);
    Route::get('auth/user',[UsersController::class,'auth']);

//    cases
    Route::post('court/create',[CourtCaseController::class,'create']);
    Route::get('court/show',[CourtCaseController::class,'show']);

    //Evidences Pictures
    Route::post('evidence/picture/add',[EvidenceController::class,'create']);
    Route::get('evidence/picture/show',[EvidenceController::class,'show_all']);

    //Document pictures
    Route::post('document/add',[DocumentController::class,'add']);
    Route::get('document/show',[DocumentController::class,'show']);

//    videos
    Route::post('video/add',[VideoController::class,'add']);
    Route::get('video/show',[VideoController::class,'show']);




    Route::group(['middleware' => 'admin'], function () {
        Route::post('admin/create',[AdminController::class,'create']);
        Route::get('admin/show',[AdminController::class,'show_admin']);
        Route::get('log/show_logs',[LogController::class,'show']);
    });
});
