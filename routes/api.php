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
Route::post('auth/verify/{id}',[UsersController::class,'verify']);
Route::get('auth/show-all',[UsersController::class,'show']);

Route::post('auth/forget_pass',[UsersController::class,'forget_pass']);
Route::post('auth/reset_password',[UsersController::class,'reset_password']);
Route::post('auth/finish_reset',[UsersController::class,'finish_reset']);





//protect the route from unauthorised user
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request)
    {return $request->user();});
    Route::get('auth/user',[UsersController::class,'auth']);



//    cases
    Route::post('court/create',[CourtCaseController::class,'create']);
    Route::get('case/single/{id}/{secret_key}',[CourtCaseController::class,'show_single']);
    Route::post('case/update/description/{case_id}',[CourtCaseController::class,'update']);
    Route::get('court/show',[CourtCaseController::class,'show']);
    Route::get('court/show_deactivated',[CourtCaseController::class,'show_deactivated']);
    Route::get('court/deactivate/{id}',[CourtCaseController::class,'deactivate']);
    Route::get('court/activate/{id}',[CourtCaseController::class,'activate']);

    //Evidences Pictures
    Route::post('evidence/picture/add/{case_id}',[EvidenceController::class,'create']);
    Route::get('evidence/picture/show/{case_id}',[EvidenceController::class,'show_all']);

    //Document pictures
    Route::post('document/add/{id}',[DocumentController::class,'add']);
    Route::get('document/show/{case_id}',[DocumentController::class,'show']);

//    videos
    Route::post('video/add/{id}',[VideoController::class,'add']);
    Route::get('video/show/{id}',[VideoController::class,'show']);




    Route::group(['middleware' => 'admin'], function () {
        Route::post('admin/create',[AdminController::class,'create']);
        Route::post('admin/edit/{id}',[AdminController::class,'edit']);
        Route::get('admin/show_cases',[AdminController::class,'show_cases']);
        Route::get('admin/show',[AdminController::class,'show_admin']);
        Route::get('log/show_logs',[LogController::class,'show']);
    });
});
