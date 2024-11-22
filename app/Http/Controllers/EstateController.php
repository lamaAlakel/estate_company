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
        $estates = Estate::whereHas('contracts', function ($query) use ($currentDate) {
            $query->where('rent_start_date', '<=', $currentDate)
                ->where('rent_end_date', '>=', $currentDate);
        })->with(['contracts' => function ($query) use ($currentDate) {
            $query->where('rent_start_date', '<=', $currentDate)
                ->where('rent_end_date', '>=', $currentDate)
                ->with('tenant');
        }])->get();
        return response()->json([
            'estates'=> $estates
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
            'message'=> 'updated successfully']);

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
            'estate'=> $estate ,
            'message'=> true,
            $estate->get()
        ]);
    }


}
