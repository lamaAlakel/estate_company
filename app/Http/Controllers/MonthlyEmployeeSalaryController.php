<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MonthlyEmployeeSalary;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

class MonthlyEmployeeSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salaries = MonthlyEmployeeSalary::with('Employee')->get();
        return response()->json([
            'salaries'=> $salaries
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
        $currentMonth = Carbon::now()->format('Y-m');
        $validated = $request->validate([
            'employee_id'=>'required | exists:employees,id',
            'main_salary'=> 'required | numeric|min:0',
            'bonus' => 'required | numeric | min:0',
            'daily_amount' => 'required|numeric|min:0',
            'notice'=> 'required|string |min:0',
            'date_should_translate_to_month'=>'required|date'
        ]);

        $existingSalary = MonthlyEmployeeSalary::where('employee_id', $validated['employee_id'])
            ->whereDate('created_at', '>=', Carbon::now()->startOfMonth())
            ->whereDate('created_at', '<=', Carbon::now()->endOfMonth())
            ->first();

        if ($existingSalary) {
            return response()->json([
                'status' => 'error',
                'message' => 'This employee has a salary for this month'
            ], 400);
        }

        $salary = MonthlyEmployeeSalary::create($validated);

        $salary->save();
        return response()->json([
            'message'=>'created successfully',
            'salary'=>$salary
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $salary = MonthlyEmployeeSalary::find($id);
        return response()->json([
            'salary'=> $salary ,
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
        $salary = MonthlyEmployeeSalary::find($id);
        if(!$salary){
            return response()->json([
                'message'=>'no employee'
            ]);
        }
        $salary->update([
            'employee_id'=> $request['employee_id'],
            'main_salary'=> $request['main_salary'],
            'bonus' =>$request['bonus'],
            'daily_amount' =>$request['daily_amount'],
            'notice'=>$request['notice'],
            'date_should_translate_to_month'=>$request['date_should_translate_to_month']
        ]);
        return response()->json([
            'message'=> 'updated successfully',
            'payment '=> $salary
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $salary = MonthlyEmployeeSalary::find($id);
        if (!$salary){
            return response()->json([
               'message'=>'no salary '
            ]);
        }
        $salary-> delete();
        return response()->json([
            'message'=>'deleted successfully'
        ]);
    }
    public function getPendingSalaries($employeeId)
    {
        $salaries = MonthlyEmployeeSalary::where('employee_id', $employeeId)
            ->with('salaryDates')
            ->get();

        $pendingSalaries = $salaries->filter(function ($salary) {
            $totalSalary = $salary->main_salary + $salary->bonus;
            $totalPaid = $salary->salaryDates->sum('amount');
            return $totalPaid < $totalSalary;
        });

        return response()->json([
            'employee_id' => $employeeId,
            'pending_salaries' => $pendingSalaries->values()
        ]);
    }
}
