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
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $request->input('month'); // Required month
        $employee = Employee::with('salaries')->findOrFail($employeeId); // Fetch employee with salaries

        $workDays = []; // To store days for the specified month
        $attendanceCount = 0; // Number of attendance days

        // Process the days data
        if ($employee->days_worked) {
            foreach ($employee->days_worked as $day) {
                // Ensure the date matches the required month
                if (str_starts_with($day, $month)) {
                    $workDays[] = $day; // Store the day
                    $attendanceCount++; // Count attendance
                }
            }
        }

        return response()->json([
            'employee_name' => $employee->name,
            'month' => $month,
            'work_days' => $workDays,
            'attendance_count' => $attendanceCount,
            'absence_count' => null, // Absence count not applicable
        ]);
    }

    public function updateDaysWorked(Request $request, $id)
    {
        // التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'days' => 'required|array', // يجب أن تكون الأيام مصفوفة
            'days.*' => 'date', // كل عنصر في الأيام يجب أن يكون تاريخًا صحيحًا
        ]);

        // العثور على الموظف
        $employee = Employee::findOrFail($id);

        // إذا كانت الأيام موجودة، دمجها مع الأيام الحالية
        $currentDays = $employee->days_worked ?? []; // جلب الأيام الحالية إذا كانت موجودة

        // دمج الأيام الجديدة مع القديمة
        $employee->days_worked = array_merge($currentDays, $validated['days']);

        // حفظ التعديلات
        $employee->save();

        return response()->json([
            'message' => 'Days worked updated successfully!',
            'days_worked' => $employee->days_worked,
        ]);
    }
}



