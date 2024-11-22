<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $fillable=[
        'full_name',
        'id_number',
        'phone_number',
        'address',
        'id_image'
    ];

    public function estates(){
        return $this->belongsToMany(Estate::class ,'rental_contracts' , 'tenant_id' , 'estate_id');
    }
    public function contracts(){
        return $this->hasMany(RentalContract::class ,'tenant_id');
    }
}
