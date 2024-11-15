<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalContract extends Model
{
    use HasFactory;
    protected $fillable=[
        'rent_start_date',
        'rent_end_date',
        'type',
        'monthly_rent',
        'estate_id',
        'tenant_id'
    ];

    public function payments(){
        $this->hasMany(RentalContractPayment::class,'rental_contract_id');
    }

    public function estate(){
        $this->belongsTo(Estate::class ,'estate_id');
    }

    public function tenant(){
        $this->belongsTo(Tenant::class ,'tenant_id');
    }
}
