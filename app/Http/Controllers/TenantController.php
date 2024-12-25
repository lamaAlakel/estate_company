<?php

namespace App\Http\Controllers;
use App\Traits\ImageTrait;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenant =Tenant::all();
        return response()->json([
            'tenants'=> $tenant
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
            $path = null ;
        if ($request->hasFile('id_image'))
        {
            $path = $this->storefile($request->file('id_image') , 'Tenant/id_image') ;
        }
     $tenant = Tenant::create([
         'full_name'=> $request['full_name'],
         'id_number' => $request['id_number'],
         'phone_number'=> $request['phone_number'],
         'address'=> $request['address'],
         'id_image' => $path  ,
     ]);
     $tenant->save();
     return response()->json([
         'message'=> 'added tenant successfully'
     ]);
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
        $tenant = Tenant::find($id);
        if(!$tenant) {
            return response()->json([
                'message' => 'no tenant'
            ]);
        }
        $path = null ;
        if ($request->hasFile('id_image'))
        {
            $path = $this->storefile($request->file('id_image') , 'Tenant/id_image') ;
        }
        $tenant->update([
            'full_name'=> $request['full_name'],
            'id_number' => $request['id_number'],
            'phone_number'=> $request['phone_number'],
            'address'=> $request['address'],
            'id_image' => $path ,
            ]);
            return response()->json([
                'message'=>'updated successfully' ,
                'tenant'=> $tenant
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant){
            return response()->json([
                'message'=> 'no tenant',
            ]);
        }
        $tenant->delete();
        return response()->json([
            'message'=>'deleted successfully',
        ]);
    }

    public function searchTenant(Request $request)
    {
        $search = $request->input('search');
        $tenant = Tenant::where('full_name', 'like', "%$search%")
            ->orWhere('id_number', 'like', "%$search%")
           ->get();
        return response()->json([
            'result'=> $tenant
        ]);
    }




}
