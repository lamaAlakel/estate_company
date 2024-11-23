<?php

namespace App\Http\Controllers;

use App\Models\RentalContract;
use App\Models\RentalContractPayment;
use Illuminate\Http\Request;

class RentalContractPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = RentalContractPayment::with('RentalContract')->get();
        return response()->json([
            'payments' => $payments
        ]);
    }


    public function indexByContract($rental_contract_id)
    {
        $payments = RentalContractPayment:: where('rental_contract_id', $rental_contract_id)->get();
        if ($payments->isEmpty()) {
            return response()->json([
                'message' => 'the contract does not payments'
            ]);
        }
        return response()->json([
            'payments' => $payments,
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
            'rental_contract_id' => 'required|exists:rental_contracts,id',
        ]);

        $rentalContract = RentalContract::find($validated['rental_contract_id']);
        if (!$rentalContract) {
            return response()->json(['message' => 'Rental contract not found'], 404);
        }

        $payment = RentalContractPayment::create($validated);

        return response()->json([
            'message' => 'Payment added successfully',
            'payment' => $payment
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $payment = RentalContractPayment:: find($id);
        if(!$payment){
            return response()->json([
                'message'=> 'no contract payment'
            ]);
        }
        $payment->update([
            'date'=> $request['date'],
            'amount'=> $request['amount'],
            'rental_contract_id'=> $request['rental_contract_id'],

        ]);
        return response()->json([
            'message'=> 'updated successfully',
            'payment '=> $payment
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = RentalContractPayment::find($id);
        if(!$payment){
            return response()->json([
                'message'=>'Contract payment does not exist '
            ]);
        }
        $payment->delete();
        return response()->json([
            'message'=>'deleted successfully'
        ]);
    }

    public function filterPayments(Request $request)
    {
        $filters = $request->only(['rental_contract_id', 'date_from', 'date_to', 'min_amount', 'max_amount']);

        $payments = RentalContractPayment::filterPayments($filters);

        if ($payments->isEmpty()) {
            return response()->json(['message' => 'No payments found'], 404);
        }

        return response()->json(['payments' => $payments], 200);
    }
}
