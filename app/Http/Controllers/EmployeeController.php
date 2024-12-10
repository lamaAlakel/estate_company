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
            'employee' => $employee
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
        $employee = Employee::create([
            'name' => $request['name'],
            'nationality' => $request['nationality'],
            'date_of_birth' => $request['date_of_birth'],
            'work_type' => $request['work_type'],
            'health_insurance_expiration_date' => $request['health_insurance_expiration_date'],
            'visa_start_date' => $request['visa_start_date'],
            'visa_expiration_date' => $request['visa_expiration_date'],
            'passport_expiration_date' => $request['passport_expiration_date'],
            'UAE_residency_number' => $request['UAE_residency_number'],
            'unified_number' => $request['unified_number'],
            'salary' => $request['salary'],
            'days_worked' => $request['days_worked'],
            'position' => $request['position']
        ]);
        $employee->save();
        return response()->json([
            'message' => 'created employee successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'no employee'
            ]);
        }
        return response()->json([
            'employee' => $employee
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
        if (!$employee) {
            return response()->json([
                'message' => 'no employee'
            ]);
        }
        $employee->update([
            'name' => $request['name'],
            'nationality' => $request['nationality'],
            'date_of_birth' => $request['date_of_birth'],
            'work_type' => $request['work_type'],
            'health_insurance_expiration_date' => $request['health_insurance_expiration_date'],
            'visa_start_date' => $request['visa_start_date'],
            'visa_expiration_date' => $request['visa_expiration_date'],
            'passport_expiration_date' => $request['passport_expiration_date'],
            'UAE_residency_number' => $request['UAE_residency_number'],
            'unified_number' => $request['unified_number'],
            'salary' => $request['salary'],
            'days_worked' => $request['days_worked'],
            'position' => $request['position']
        ]);
        return response()->json([
            'message' => 'update successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'message' => 'no employee'
            ]);
        }
        $employee->delete();
        return response()->json([
            'message' => 'deleted successfully'
        ]);
    }

    public function searchEmployee(Request $request)
    {
        $search = $request->input('search');
        $employee = Employee::where('name', 'like', "%$search%")
            ->get();
        return response()->json([
            'result' => $employee
        ]);

    }

    public function getEmployeeWorkDays(Request $request, $employeeId)
    {
        // التحقق من أن المستخدم أرسل الشهر المطلوب
        $request->validate([
            'month' => 'required|date_format:Y-m', // صيغة الشهر المطلوب (YYYY-MM)
        ]);

        $month = $request->input('month'); // الشهر المطلوب
        $employee = Employee::with('salaries')->findOrFail($employeeId); // جلب الموظف مع المرتبات

        $workDays = []; // لتخزين الأيام حسب الشهر
        $attendanceCount = 0; // عدد أيام الحضور
        $absenceCount = 0; // عدد أيام الغياب

        // معالجة بيانات الأيام
        if ($employee->days_worked) {
            foreach ($employee->days_worked as $day => $status) {
                // التأكد أن التاريخ يطابق الشهر المطلوب
                if (str_starts_with($day, $month)) {
                    $workDays[$day] = $status; // تخزين اليوم والحالة
                    if ($status === 'present') {
                        $attendanceCount++; // حساب الحضور
                    } elseif ($status === 'absent') {
                        $absenceCount++; // حساب الغياب
                    }
                }
            }
        }

        return response()->json([
            'employee_name' => $employee->name,
            'month' => $month,
            'work_days' => $workDays,
            'attendance_count' => $attendanceCount,
            'absence_count' => $absenceCount,
        ]);
    }
}



