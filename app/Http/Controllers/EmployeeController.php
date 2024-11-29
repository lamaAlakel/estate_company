<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use http\Env\Response;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Employee::all();
        return response()->json([
            'employee'=>$employee
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
        $employee =Employee::create([
            'name'=>$request['name'],
            'nationality'=>$request['nationality'],
            'date_of_birth'=>$request['date_of_birth'],
            'work_type'=>$request['work_type'],
            'health_insurance_expiration_date'=>$request['health_insurance_expiration_date'],
            'visa_start_date'=>$request['visa_start_date'],
            'visa_expiration_date'=>$request['visa_expiration_date'],
            'passport_expiration_date'=>$request['passport_expiration_date'],
            'UAE_residency_number'=>$request['UAE_residency_number'],
            'unified_number'=>$request['unified_number'],
            'salary'=>$request['salary'],
            'days_worked'=>$request['days_worked']
        ]);
        $employee->save();
        return response()->json([
            'message'=>'created employee successfully',
            'employee'=>$employee
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::find($id);
        if(!$employee){
            return response()->json([
                'message'=>'no employee'
            ]);
        }
        return response()->json([
            'employee'=>$employee
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
        $employee = Employee::find($id);
        if(!$employee){
            return response()->json([
                'message'=>'no employee'
            ]);
        }
        $employee->update([
            'name'=>$request['name'],
            'nationality'=>$request['nationality'],
            'date_of_birth'=>$request['date_of_birth'],
            'work_type'=>$request['work_type'],
            'health_insurance_expiration_date'=>$request['health_insurance_expiration_date'],
            'visa_start_date'=>$request['visa_start_date'],
            'visa_expiration_date'=>$request['visa_expiration_date'],
            'passport_expiration_date'=>$request['passport_expiration_date'],
            'UAE_residency_number'=>$request['UAE_residency_number'],
            'unified_number'=>$request['unified_number'],
            'salary'=>$request['salary'],
            'days_worked'=>$request['days_worked'] ,
        ]);
        return response()->json([
            'message'=>'update successfully',
            'employee'=>$employee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::find($id);
        if (!$employee){
            return response()->json([
            'message'=> 'no employee'
            ]);
        }
        $employee->delete();
        return response()->json([
            'message'=>'deleted successfully'
        ]);
    }
    public function searchEmployee(Request $request){
        $search = $request->input('search');
        $employee = Employee::where('name', 'like', "%$search%")
            ->get();
        return response()->json([
            'result'=> $employee
        ]);
    }
}
