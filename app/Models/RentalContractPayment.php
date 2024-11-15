<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalContractPayment extends Model
{
    use HasFactory;
    protected $fillable=[
        'date',
        'amount',
        'rental_contract_id'
    ];

    public function rentalContract(){
        return $this->belongsTo(RentalContract ::class ,'rental_contract_id' );
    }
}
