<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    use HasFactory;
    protected $fillable=[
        'code','type'

    ];

    public function tenants(){
        $this->belongsToMany(Tenant::class,'rental_contracts' ,'estate_id' ,'tenant_id');
    }

    public function invoices(){
        $this->hasMany(Invoice::class ,'estate_id');
    }

    public function contracts(){
        $this->hasMany(RentalContract::class ,'estate_id');
    }

}
