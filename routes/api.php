<?php

use App\Http\Controllers\api\auth\UserAuthController;
use App\Http\Controllers\api\BinController;
use App\Http\Controllers\api\TrashController;
use App\Http\Controllers\api\TruckController;
use App\Http\Controllers\OptimizationController;    

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



Route::controller(UserAuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::delete('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('/user', 'getUser')->middleware('auth:sanctum');
    Route::put('/update', 'updateUser')->middleware('auth:sanctum');
    Route::put('/update-position', 'updateUserPosition')->middleware('auth:sanctum');
});


//checkTruckLocation
Route::post('/optimize', [OptimizationController::class, 'optimize']);

Route::post('/check-truck-location/{truck}', [TruckController::class, 'checkTruckLocation'])->middleware('auth:sanctum');
Route::post('/nearby-bins', [BinController::class, 'nearbyBins'])->middleware('auth:sanctum');
Route::put('/update-bin-position/{bin}', [BinController::class, 'updateBinPosition'])->middleware('auth:sanctum');
Route::apiResource('bins', BinController::class)->middleware('auth:sanctum');
Route::apiResource('trash', TrashController::class)->middleware('auth:sanctum');
