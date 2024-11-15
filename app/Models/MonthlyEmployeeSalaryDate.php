<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyEmployeeSalaryDate extends Model
{
    use HasFactory;
    protected $fillable=[
        'monthly_employee_salary_id',
        'date',
        'amount'
    ];
    public function salary(){
        $this->belongsTo(MonthlyEmployeeSalary::class , 'monthly_employee_salary_id');
    }
}
