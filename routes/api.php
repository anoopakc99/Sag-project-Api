<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AitRegistrationController;

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

// User authentication route
Route::post('/login-ait', [ApiController::class, 'loginAitAction']);

//Formar Reagistration
Route::post('/farmer-registers', [ApiController::class, 'farmerRegister']);

//formar Upadation
Route::post('/update-farmer-records', [ApiController::class, 'updateFarmerRecord']);

//save Ai records
Route::post('/save-ai-record', [ApiController::class, 'saveAiRecord']);

Route::post('/register-ait', [AitRegistrationController::class, 'addNewUser']);

