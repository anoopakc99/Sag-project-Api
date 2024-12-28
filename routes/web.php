<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AitRegistrationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/ait-registration', [AITRegistrationController::class, 'showForm']);
Route::get('/get-districts', [AITRegistrationController::class, 'getDistricts']);
Route::get('/get-sales-persons', [AITRegistrationController::class, 'getSalesPersons']);
Route::get('/get-tehsils', [AITRegistrationController::class, 'getTehsils']);
Route::post('/submit-ait-registration', [AITRegistrationController::class, 'submitForm']);

