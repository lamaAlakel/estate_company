<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyEmployeeSalary extends Model
{
    use HasFactory;
    protected $fillable=[
        'employee_id',
        'main_salary',
        'bonus',
        'daily_amount',
        'notice',
        'date_should_translate_to_month'
    ];
    public function employee(){
        return $this->belongsTo(Employee::class ,'employee_id');
    }
    public function salaryDates(){
        return $this->hasMany(MonthlyEmployeeSalaryDate::class ,'monthly_employee_salary_id');
    }
}
