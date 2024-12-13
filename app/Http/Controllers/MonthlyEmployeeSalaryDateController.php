<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MonthlyEmployeeSalary;
use App\Models\MonthlyEmployeeSalaryDate;
use Illuminate\Http\Request;

class MonthlyEmployeeSalaryDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $date = MonthlyEmployeeSalaryDate::with('salary')->get();
        return response()->json([
            'date'=> $date
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'monthly_employee_salary_id' => 'required|exists:monthly_employee_salaries,id',
        ]);
        $monthly_employee_salary = MonthlyEmployeeSalary::find($validated['monthly_employee_salary_id']) ;
        if(!$monthly_employee_salary){
            return response()->json([
                'message'=>'monthly_employee_salary not found'
            ],404);
        }
        $date = MonthlyEmployeeSalaryDate::create($validated);
        return response()->json([
            'message'=> 'created successfully',
            'date'=>$date
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $date = MonthlyEmployeeSalaryDate::find($id);
        return response()->json([
            'dates'=>$date
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function showSalaryPayments($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        $salaries = $employee->salaries()
            ->with(['salaryDates' => function ($query) {
                $query->select('monthly_employee_salary_id', 'date', 'amount');
            }])
            ->get()
            ->map(function ($salary) {
                return [
                    'main_salary' => $salary->main_salary,
                    'bonus' => $salary->bonus,
                    'daily_amount' => $salary->daily_amount,
                    'notice' => $salary->notice,
                    'salary_dates' => $salary->salaryDates
                ];
            });

        return response()->json([
            'employee_name' => $employee->name,
            'salary_payments' => $salaries
        ]);
    }
}
