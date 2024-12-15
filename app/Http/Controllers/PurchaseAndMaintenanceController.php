<?php

namespace App\Http\Controllers;

use App\Models\PurchaseAndMaintenance;
use Illuminate\Http\Request;

class PurchaseAndMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchase = PurchaseAndMaintenance::all();
        return response()->json([
            'purchase'=>$purchase
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
        $purchase = PurchaseAndMaintenance::create([
            'name'=>$request['name'],
            'date'=>$request['date'],
            'quantity'=>$request['quantity'],
            'unit_cost'=>$request['unit_cost'],
            'total_paid'=>$request['total_paid']
        ]);
        $purchase->save();
        return response()->json([
           'message'=>'created successfully',
           'purchase'=>$purchase
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchase = PurchaseAndMaintenance::find($id);
        if(!$purchase){
            return response()->json([
                'message'=>'purchase not found'
            ]);
        }
        return response()->json([
            'purchase'=> $purchase
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
        $purchase = PurchaseAndMaintenance::find($id);
        if(!$purchase){
            return response()->json([
                'message'=>'purchase not found'
            ]);
        }
        $purchase->update([
            'name'=>$request['name'],
            'date'=>$request['date'],
            'quantity'=>$request['quantity'],
            'unit_cost'=>$request['unit_cost'],
            'total_paid'=>$request['total_paid']
        ]);
        return response()->json([
           'message'=>'update successfully',
           'purchase'=>$purchase
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchase = PurchaseAndMaintenance::find($id);
        if(!$purchase){
            return response()->json([
                'message'=>'purchase not found'
            ]);
        }
        $purchase->delete();
        return response()->json([
            'message'=> 'deleted successfully'
            ]);
    }
}
