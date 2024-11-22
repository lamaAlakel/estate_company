<?php

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

Route::apiResource('estate', \App\Http\Controllers\EstateController::class);
Route::post('/filter-estates', [\App\Http\Controllers\EstateController::class, 'Filter']);


Route::apiResource('contract', \App\Http\Controllers\RentalContractController::class);
Route::get('contract/index/{estate_id}',[\App\Http\Controllers\RentalContractController::class,'indexByEstate']);
Route::post('filter-contract',[\App\Http\Controllers\RentalContractController::class,'filterContracts']);


Route::apiResource('tenant', \App\Http\Controllers\TenantController::class);


Route::apiResource('contractPayment', \App\Http\Controllers\RentalContractPaymentController::class);
Route::get('contractPayment/index/{contract_id}',[\App\Http\Controllers\RentalContractPaymentController::class,'indexByContract']);
Route::post('filter-contractPayment',[\App\Http\Controllers\RentalContractPaymentController::class,'filterPayments']);
