<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Employee;
use App\Models\Estate;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\MonthlyEmployeeSalary;
use App\Models\MonthlyEmployeeSalaryDate;
use App\Models\PurchaseAndMaintenance;
use App\Models\RentalContract;
use App\Models\RentalContractPayment;
use App\Models\Tenant;
use App\Models\User;
use Database\Factories\EmployeeFactory;
use Database\Factories\RentalContractFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Estate::factory('10')->create();
        Tenant::factory('10')->create();
        RentalContract::factory('10')->create();
        RentalContractPayment::factory('10')->create();
        Employee::factory('10')->create();
        MonthlyEmployeeSalary::factory('10')->create();
        MonthlyEmployeeSalaryDate::factory('10')->create();
        Invoice::factory('10')->create();
        InvoicePayment::factory('10')->create();
        PurchaseAndMaintenance::factory('10')->create();



        User::create([
              'name' => 'admin' ,
              'email' => 'admin@gmail.com' ,
              'password' => Hash::make('password')
            ]) ;
    }
}
