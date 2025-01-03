<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monthly_employee_salary_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('monthly_employee_salary_id')->constrained('monthly_employee_salaries')->cascadeOnDelete();
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_employee_salary_dates');
    }
};
