<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\RentController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('mobil', [MobilController::class, 'listmobil']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('mobil', [MobilController::class, 'addMobil']);
    Route::get('mobil/search', [MobilController::class, 'searchMobil']);

    Route::post('rent', [RentController::class, 'rentMobil']);
    Route::post('rent/return', [RentController::class, 'returnMobil']);
    Route::get('rent', [RentController::class, 'viewRent']);

    Route::get('users', [AuthController::class, 'getUsers']);
});
