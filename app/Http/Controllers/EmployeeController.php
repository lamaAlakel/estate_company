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
            'days_worked' => [],
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

        $month = $request->input('month'); // Month requested
        $employee = Employee::with('salaries')->findOrFail($employeeId);
        // Ensure days_worked is always treated as an array
        $daysWorked = is_array($employee->days_worked) ? $employee->days_worked : json_decode($employee->days_worked, true) ?? [];

        // Filter days_worked for the specified month
        $workDays = array_filter($daysWorked, function ($day) use ($month) {
            return str_starts_with($day, $month);
        });

        $attendanceCount = count($workDays); // Number of attendance days
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int) explode('-', $month)[1], (int) explode('-', $month)[0]); // Total days in month
        $absenceCount = $daysInMonth - $attendanceCount;

        return response()->json([
            'employee_name' => $employee->name,
            'month' => $month,
            'work_days' => array_values($workDays), // Reset array keys
            'attendance_count' => $attendanceCount,
            'absence_count' => $absenceCount,
        ]);
    }

    public function updateDaysWorked(Request $request, $id)
    {
        // Validate input
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m', // Month format (YYYY-MM)
            'days' => 'required|array',           // Days worked should be an array
            'days.*' => 'date_format:Y-m-d',      // Each day must be a valid date
        ]);

        // Find the employee
        $employee = Employee::findOrFail($id);

        $month = $validated['month'];  // Month sent by the user
        $newDays = $validated['days']; // Days worked for the given month

        // Filter out existing days that do not belong to the specified month
        $existingDays = $employee->days_worked ?? [];
        $updatedDays = array_filter($existingDays, function ($day) use ($month) {
            return !str_starts_with($day, $month); // Keep only days outside the given month
        });

        // Merge the new days for the month
        $updatedDays = array_merge($updatedDays, $newDays);

        // Update the employee's days_worked
        $employee->days_worked = $updatedDays;
        $employee->save();

        return response()->json([
            'message' => 'Days worked updated successfully!',
            'month' => $month,
            'days_worked' => $updatedDays,
        ]);
    }

    public function getEmployeesWithExpirations()
    {
        // Get the current date and 2 months from now
        $currentDate = Carbon::now();
        $thresholdDate = $currentDate->copy()->addMonths(2);

        // Query employees and calculate days to expiration
        $employees = Employee::select(
            'name',
            'passport_expiration_date',
            'visa_expiration_date'
        )->get()->map(function ($employee) use ($currentDate, $thresholdDate) {
            // Calculate days to passport expiration
            $passportDays = $employee->passport_expiration_date
                ? Carbon::parse($employee->passport_expiration_date)->diffInDays($currentDate) *
                (Carbon::parse($employee->passport_expiration_date)->isPast() ? -1 : 1)
                : null;

            // Calculate days to visa expiration
            $visaDays = $employee->visa_expiration_date
                ? Carbon::parse($employee->visa_expiration_date)->diffInDays($currentDate) *
                (Carbon::parse($employee->visa_expiration_date)->isPast() ? -1 : 1)
                : null;

            return [
                'name' => $employee->name,
                'passport_expiration_date' => $employee->passport_expiration_date,
                'days_to_passport_expiration' => $passportDays,
                'visa_expiration_date' => $employee->visa_expiration_date,
                'days_to_visa_expiration' => $visaDays,
                'passport_expired' => $passportDays !== null && $passportDays < 0,
                'visa_expired' => $visaDays !== null && $visaDays < 0,
                'passport_within_2_months' => $passportDays !== null && $passportDays <= 60 && $passportDays >= 0,
                'visa_within_2_months' => $visaDays !== null && $visaDays <= 60 && $visaDays >= 0,
            ];
        });

        // Filter employees for expired or within 2 months
        $filteredEmployees = $employees->filter(function ($employee) {
            return $employee['passport_expired'] ||
                $employee['visa_expired'] ||
                $employee['passport_within_2_months'] ||
                $employee['visa_within_2_months'];
        });

        // Return JSON response
        return response()->json([
            'status' => 'success',
            'data' => $filteredEmployees->values(),
        ]);
    }
}
