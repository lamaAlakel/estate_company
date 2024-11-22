<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'nationality',
        'date_of_birth',
        'work_type',
        'health_insurance_expiration_date',
        'visa_start_date',
        'visa_expiration_date',
        'passport_expiration_date',
        'UAE_residency_number',
        'unified_number',
        'salary',
        'days_worked'
        ];

    public function salaries(){
        return $this->hasMany(MonthlyEmployeeSalary::class ,'employee_id');
    }
    protected $casts =[
        'days_worked' => 'array'
    ] ;
}
