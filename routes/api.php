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
Route::post('/statistics/rented-estates', [\App\Http\Controllers\EstateController::class, 'getRentedEstatesWithPayments']);
Route::post('/statistics/getFinancialReport', [\App\Http\Controllers\EstateController::class, 'getFinancialReport']);
Route::get('estates-expiring-contracts', [\App\Http\Controllers\EstateController::class, 'getEstatesWithExpiringContracts']);


Route::apiResource('contract', \App\Http\Controllers\RentalContractController::class);
Route::get('contract/index/{estate_id}',[\App\Http\Controllers\RentalContractController::class,'indexByEstate']);
Route::post('filter-contract',[\App\Http\Controllers\RentalContractController::class,'filterContracts']);
Route::get('contract/show/{client_id}',[\App\Http\Controllers\RentalContractController::class ,'showContractBYTenant']);



Route::apiResource('contractPayment', \App\Http\Controllers\RentalContractPaymentController::class);
Route::get('contractPayment/index/{contract_id}',[\App\Http\Controllers\RentalContractPaymentController::class,'indexByContract']);
Route::post('contractPayment/filter',[\App\Http\Controllers\RentalContractPaymentController::class,'scopeFilter']);



Route::apiResource('tenant', \App\Http\Controllers\TenantController::class);
Route::post('tenant/search',[\App\Http\Controllers\TenantController::class ,'searchTenant']);
Route::post('tenant/{id}',[\App\Http\Controllers\TenantController::class ,'update']);



Route::apiResource('employee', \App\Http\Controllers\EmployeeController::class);
Route::post('employee/search',[\App\Http\Controllers\EmployeeController::class ,'searchEmployee']);
Route::post('employee/workedDays/{employeeId}',[\App\Http\Controllers\EmployeeController::class ,'getEmployeeWorkDays']);
Route::put('employee/update/workedDays/{employeeId}',[\App\Http\Controllers\EmployeeController::class ,'updateDaysWorked']);
Route::get('/employees-expiration', [\App\Http\Controllers\EmployeeController::class, 'getEmployeesWithExpirations']);


Route::apiResource('salary', \App\Http\Controllers\MonthlyEmployeeSalaryController::class);
Route::get('salary/pendingSalary/{id}', [\App\Http\Controllers\MonthlyEmployeeSalaryController::class , 'getPendingSalaries']);



Route::apiResource('salaryDate', \App\Http\Controllers\MonthlyEmployeeSalaryDateController::class);
Route::get('salaryDate/show/{id}',[\App\Http\Controllers\MonthlyEmployeeSalaryDateController::class ,'showSalaryPayments']);



Route::apiResource('invoice',\App\Http\Controllers\InvoiceController::class);
Route::post('invoice/filter',[\App\Http\Controllers\InvoiceController::class,'filter']);
Route::get('/invoice/payments/{id}', [\App\Http\Controllers\InvoiceController::class, 'getInvoicePayments']);
Route::post('/invoices/unpaid', [\App\Http\Controllers\InvoiceController::class, 'getUnpaidInvoices']);

Route::apiResource('invoicePayment',\App\Http\Controllers\InvoicePaymentController::class);



Route::apiResource('purchase',\App\Http\Controllers\PurchaseAndMaintenanceController::class);


