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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nationality');
            $table->date('date_of_birth');
            $table->enum('work_type',['on_site','remotely']);
            $table->date('health_insurance_expiration_date');
            $table->date('visa_start_date');
            $table->date('visa_expiration_date');
            $table->date('passport_expiration_date');
            $table->string('UAE_residency_number');
            $table->string('unified_number');
            $table->integer('salary');
            $table->json('days_worked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
