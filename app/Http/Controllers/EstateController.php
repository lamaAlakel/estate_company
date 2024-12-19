<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use Carbon\Carbon;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class EstateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentDate = Carbon::now();

        // Get estates with current contracts and their details
        $estatesWithCurrentContracts = Estate::whereHas('contracts', function ($query) use ($currentDate) {
            $query->where('rent_start_date', '<=', $currentDate)
                ->where('rent_end_date', '>=', $currentDate);
        })->with(['contracts' => function ($query) use ($currentDate) {
            $query->where('rent_start_date', '<=', $currentDate)
                ->where('rent_end_date', '>=', $currentDate)
                ->with('tenant');
        }])->get();

        // Get estates without current contracts
        $estatesWithoutCurrentContracts = Estate::whereDoesntHave('contracts', function ($query) use ($currentDate) {
            $query->where('rent_start_date', '<=', $currentDate)
                ->where('rent_end_date', '>=', $currentDate);
        })->get();

        // Combine both lists
        $estates = $estatesWithCurrentContracts->merge($estatesWithoutCurrentContracts);

        return response()->json([
            'estates' => $estates
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $estates = Estate::create([
            'code'=>$request['code'],
            'type'=>$request['type'],
        ]);
        $estates->save();

        return Response()->json([
            'message'=>'estate created successfully',
            'estates'=> $estates
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $estate = Estate::find($id);

        if (!$estate) {
            return response()->json(['message' => 'Estate not found'], 404);
        }

        return response()->json(['estate' => $estate], 200);

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
        $estate = Estate::find($id);
        if (!$estate){
            return response()->json([
                'message'=> 'no estate'
            ]);
        } $estate ->update([
            'code'=>$request['code'],
            'type'=>$request['type']
            ]);

        return response()->json([
            'message'=> 'updated successfully' ,
            'estate' => $estate
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $estate = Estate::find( $id);
        if(!$estate){
          return response()->json([
              'message'=>'no estate',
          ],404);
        }
        $estate->delete();
        return response()->json([
            'message'=>'deleted successfully'
        ],200);
    }


    public function Filter(Request $request)
    {
        $estate = Estate::
            when(isset($request['type']), function ($q) use ($request) {
                $q->where('type', $request['type']);
            })
            ->when(isset($request['code']), function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request['code'] . '%');
            })
            ->when(isset($request['has_tenants']), function ($q) use ($request) {
                if ($request['has_tenants']) {
                    $q->whereHas('tenants');
                } else {
                    $q->whereDoesntHave('tenants');
                }
            });
        return response()->json([
            'data'=> $estate->get(),
        ]);
    }

    public function getStatistics(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        // Get overall statistics
        $rentedCount = Estate::whereHas('contracts', function ($query) use ($month, $year) {
            $query->whereYear('start_date', $year)
                ->whereMonth('start_date', $month);
        })->count();

        $totalEstates = Estate::count();
        $emptyCount = $totalEstates - $rentedCount;

        $totalIncome = Estate::with('invoices')
            ->get()
            ->flatMap(function ($estate) use ($month, $year) {
                return $estate->invoices->filter(function ($invoice) use ($month, $year) {
                    return $invoice->date->year == $year && $invoice->date->month == $month;
                });
            })->sum('amount');

        // Get statistics by type
        $statisticsByType = Estate::with(['contracts', 'invoices'])
            ->get()
            ->groupBy('type')
            ->map(function ($group) use ($month, $year) {
                $rentedCount = $group->pluck('contracts')->flatten()
                    ->filter(function ($contract) use ($month, $year) {
                        return $contract->start_date->year == $year && $contract->start_date->month == $month;
                    })->count();

                $totalIncome = $group->pluck('invoices')->flatten()
                    ->filter(function ($invoice) use ($month, $year) {
                        return $invoice->date->year == $year && $invoice->date->month == $month;
                    })->sum('amount');

                return [
                    'rented_count' => $rentedCount,
                    'empty_count' => $group->count() - $rentedCount,
                    'total_income' => $totalIncome,
                ];
            });

        // Return the result as a JSON response
        return response()->json([
            'overall' => [
                'rented_count' => $rentedCount,
                'empty_count' => $emptyCount,
                'total_income' => $totalIncome,
            ],
            'by_type' => $statisticsByType,
        ]);
    }



    public function getRentedEstatesWithPayments(Request $request)
    {
        $month = $request->input('month'); // Format: YYYY-MM
        $startDate = $month ? $month . '-01' : now()->startOfMonth()->toDateString();
        $endDate = $month ? date('Y-m-t', strtotime($startDate)) : now()->endOfMonth()->toDateString();

        // Define all possible types
        $estateTypes = ['room', 'office', 'commercial_shop'];

        // Query to count rented estates grouped by type
        $rentedEstates = Estate::select('type', DB::raw('COUNT(*) as count'))
            ->whereHas('contracts', function ($query) use ($startDate, $endDate) {
                $query->where('rent_start_date', '<=', $endDate)
                    ->where('rent_end_date', '>=', $startDate);
            })
            ->groupBy('type')
            ->pluck('count', 'type');

        // Query to sum payments grouped by estate type
        $estatePayments = Estate::join('rental_contracts', 'estates.id', '=', 'rental_contracts.estate_id')
            ->join('rental_contract_payments', 'rental_contracts.id', '=', 'rental_contract_payments.rental_contract_id')
            ->whereBetween('rental_contract_payments.date', [$startDate, $endDate])
            ->select('estates.type', DB::raw('SUM(rental_contract_payments.amount) as total_payment'))
            ->groupBy('estates.type')
            ->pluck('total_payment', 'estates.type');

        // Initialize all types with 0 for both count and payment
        $result = [];
        foreach ($estateTypes as $type) {
            $result[$type] = [
                'count' => $rentedEstates[$type] ?? 0,
                'total_payment' => $estatePayments[$type] ?? 0,
            ];
        }

        // Return JSON response with grouped data
        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    public function getFinancialReport(Request $request)
    {
        // Validate and extract start and end dates
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            return response()->json(['status' => 'error', 'message' => 'Start and end dates are required'], 400);
        }

        // Incoming: Sum of rental contract payments
        $incoming = DB::table('rental_contract_payments')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Outgoing: Monthly employee salaries (from salary dates)
        $employeeSalaryOut = DB::table('monthly_employee_salary_dates')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Outgoing: Invoice payments
        $invoicePaymentOut = DB::table('invoice_payments')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Outgoing: Purchases and maintenance
        $purchaseAndMaintenanceOut = DB::table('purchase_and_maintenances')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_paid');

        // Calculate totals
        $totalOutgoing = $employeeSalaryOut + $invoicePaymentOut + $purchaseAndMaintenanceOut;
        $total = $incoming - $totalOutgoing;

        // Response structure
        $result = [
            'status' => 'success',
            'data' => [
                'incoming' => $incoming,
                'outgoing' => [
                    'employee_salaries' => $employeeSalaryOut,
                    'invoice_payments' => $invoicePaymentOut,
                    'purchases_and_maintenance' => $purchaseAndMaintenanceOut,
                    'total_outgoing' => $totalOutgoing,
                ],
                'total' => $total,
            ]
        ];

        // Return JSON response
        return response()->json($result);
    }
    public function getEstatesWithExpiringContracts(Request $request)
    {
        // Get paginated estates with current contracts nearing expiration
        $estates = Estate::with(['contracts' => function ($query) {
            $query->where('rent_end_date', '>=', now())
                ->orderBy('rent_end_date', 'asc'); // Sort by expiration date
        }])
            ->whereHas('contracts', function ($query) {
                $query->where('rent_end_date', '>=', now());
            })
            ->paginate(10); // Use pagination with 10 items per page

        // Format response
        return response()->json([
            'status' => 'success',
            'data' => $estates
        ]);
    }
}
