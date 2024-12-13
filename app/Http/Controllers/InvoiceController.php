<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $invoices = Invoice::all();
    return response()->json([
        'invoice'=>$invoices ,
    ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $invoice = Invoice::create([
            'estate_id'=>$request['estate_id'],
            'meter_number'=>$request['meter_number'],
            'account_number'=>$request['account_number'],
            'total_invoice_amount'=>$request['total_invoice_amount'],
            'type'=>$request['type'],
            'date'=>$request['date']
        ]);
        $invoice->save();
        return response()->json([
            'message'=>'created successfully',
            'invoice'=>$invoice
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::find($id);
        return response()->json([
            'invoice'=>$invoice
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
        $invoice = Invoice::find($id);
        if(!$invoice){
            return response()->json([
                'message'=>'not found'
            ]);
        }
        $invoice->update([
            'estate_id'=>$request['estate_id'],
            'meter_number'=>$request['meter_number'],
            'account_number'=>$request['account_number'],
            'total_invoice_amount'=>$request['total_invoice_amount'],
            'type'=>$request['type'],
            'date'=>$request['date'],
        ]);
        return response()->json([
            'message'=>'updated successfully',
            'invoice'=>$invoice
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice){
            return response()->json([
                'message'=>'invoice not found',
            ]);
        }
        $invoice->delete();
        return response()->json([
            'message'=>'deleted successfully',
        ]);
    }

}
