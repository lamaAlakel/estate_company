<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
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
            'days_worked'=>$request['days_worked'],
            'position'=>$request['position']
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
            'position'=>$request['position']
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
    public function getAttendance(Request $request, $employeeId)
    {
        // التحقق من الموظف
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // التحقق من وجود الشهر المطلوب
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return response()->json(['error' => 'Month and year are required'], 400);
        }

        // جلب أيام العمل الخاصة بالموظف
        $daysWorked = collect($employee->days_worked);

        // تصفية الأيام حسب الشهر المطلوب
        $attendance = $daysWorked->filter(function ($day) use ($month, $year) {
            $date = Carbon::parse($day['date']);
            return $date->month == $month && $date->year == $year;
        });

        // تنسيق البيانات للتقويم
        $calendar = [];
        $totalPresent = 0;
        $totalAbsent = 0;

        foreach ($attendance as $day) {
            $status = $day['status']; // الحالة (حضور/غياب)
            $calendar[] = [
                'date' => $day['date'],
                'status' => $status
            ];

            if ($status == 'present') {
                $totalPresent++;
            } elseif ($status == 'absent') {
                $totalAbsent++;
            }
        }

        return response()->json([
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year,
            'calendar' => $calendar,
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent
        ]);
    }

}
