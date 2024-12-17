<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use Carbon\Carbon;
use http\Client\Response;
use Illuminate\Http\Request;
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

        // Validate the request inputs
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:' . now()->year,
        ]);

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



}
