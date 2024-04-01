<?php

use App\Http\Controllers\api\TruckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\auth\CollectorAuthController;
/*
|--------------------------------------------------------------------------
| API Routes For Collector
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(CollectorAuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::delete('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('/', 'getCollector')->middleware('auth:sanctum');
    Route::put('/update', 'updateCollector')->middleware('auth:sanctum');
});

//checkTruckLocation
Route::post('/check-truck-location/{truck}', [TruckController::class, 'checkTruckLocation'])->middleware('auth:sanctum');
//updateTruckPosition
Route::put('/update-truck-position/{truck}', [TruckController::class, 'updateTruckPosition'])->middleware('auth:sanctum');
Route::apiResource('trucks', TruckController::class)->middleware('auth:sanctum');
