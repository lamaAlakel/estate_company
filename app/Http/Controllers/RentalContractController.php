<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\RentalContract;
use App\Models\Tenant;
use http\Env\Response;
use Illuminate\Http\Request;

class RentalContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contract = RentalContract::all();
        return response()->json([
            'contract'=> $contract ,
        ]);
    }

    public function indexByEstate($estate)
    {
        $estate = Estate::find($estate);
        if (!$estate) {
            return response()->json([
                'message' => 'The estate not found'
            ], 404);
        }

        // Load contracts along with the tenant relationship
        $contracts = $estate->contracts()->with('tenant')->get();

        if ($contracts->isEmpty()) {
            return response()->json([
                'contract' => []
            ]);
        }

        return response()->json([
            'contract' => $contracts
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
        $contract = RentalContract::create([
            'rent_start_date'=>$request['rent_start_date'],
            'rent_end_date'=>$request['rent_end_date'],
            'type'=>$request['type'],
            'monthly_rent'=>$request['monthly_rent'],
            'estate_id'=>$request['estate_id'],
            'tenant_id'=>$request['tenant_id']
        ]);
        $contract->save();

        return Response()->json([
            'message'=>'contract created successfully',
            'contract'=> $contract
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contract = RentalContract::find($id);
        if (!$contract) {
            return response()->json(['message' => 'contract not found'], 404);
        }
        return response()->json([
           'contract'=> $contract ,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contract = RentalContract:: find($id);
        if(!$contract){
            return response()->json([
                'message'=> 'no contract'
            ]);
        }
        $contract->update([
            'rent_start_date'=> $request['rent_start_date'],
            'rent_end_date' =>$request['rent_end_date'],
            'type'=>$request['type'],
            'monthly_rent' =>$request['monthly_rent'],
            'estate_id' =>$request['estate_id'],
            'tenant_id'=>$request['tenant_id']
        ]);
        return response()->json([
            'message'=> 'updated successfully',
            'contract'=> $contract
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contract = RentalContract::find( $id);
        if(!$contract){
            return response()->json([
                'message'=>'no contract',
            ],404);
        }
        $contract->delete();
        return response()->json([
            'message'=>'deleted successfully'
        ],200);
    }


    public static function filterContracts(Request $request)
    {
        $query = RentalContract::query();

        if (isset($request['rent_start_date'])) {
            $query->where('rent_start_date', '>=', $request['rent_start_date']);
        }

        if (isset($request['rent_end_date'])) {
            $query->where('rent_end_date', '<=', $request['rent_end_date']);
        }

        if (isset($request['type'])) {
            $query->where('type', $request['type']);
        }

        if (isset($request['monthly_rent_min'])) {
            $query->where('monthly_rent', '>=', $request['monthly_rent_min']);
        }

        if (isset($request['monthly_rent_max'])) {
            $query->where('monthly_rent', '<=', $request['monthly_rent_max']);
        }

        if (isset($request['tenant_id'])) {
            $query->where('tenant_id', $request['tenant_id']);
        }

        if (isset($request['estate_id'])) {
            $query->where('estate_id', $request['estate_id']);
        }

        return response()->json([
            'query'=> $query->get() ,
        ]);
    }

    public function showContractBYTenant($tenant_id){

        $contract = RentalContract::where('tenant_id',$tenant_id)->with('estate:id,code')->get();
        if (!$contract){
            return response()->json([
                'message'=>'not found'
            ]);
        }
        return response()->json([
            'contract'=>$contract
        ]);

    }

}
