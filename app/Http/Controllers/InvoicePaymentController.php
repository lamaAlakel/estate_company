<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use http\Env\Response;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoice_payment = InvoicePayment::with('invoice')->get();
        return response()->json([
            'invoice_payment'=> $invoice_payment
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
            'invoice_id' => 'required|exists:invoices,id',
        ]);
        $invoice =Invoice::find($validated['invoice_id']) ;
        if(!$invoice){
            return response()->json([
                'message'=>'invoice not found'
            ],404);
        }
        $invoice_payment = InvoicePayment::create($validated);
        return response()->json([
            'message'=> 'created successfully',
            'payment'=>$invoice_payment
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice_payment = InvoicePayment::find($id);
        return response()->json([
            'invoice_payment'=>$invoice_payment
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
        $invoicePayment = InvoicePayment::find($id);
        if (!$invoicePayment){
            return response()->json([
                'message'=>'not found'
            ]);
        }
        $invoicePayment->update([
            'date'=>$request['date'],
            'amount'=>$request['amount'],
            'invoice_id'=>$request['invoice_id']
        ]);
        return response()->json([
            'message'=>'updated successfully',
           'payment' => $invoicePayment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = InvoicePayment::find($id);
        if(!$payment){
            return response()->json([
                'message'=>'not found '
            ]);
        }
        $payment->delete();
        return response()->json([
            'message'=>'deleted successfully'
        ]);
    }
}
