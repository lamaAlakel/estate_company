<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $fillable=[
        'full_name','id_number','phone_number','address','id_image'
    ];

    public function estates(){
        $this->belongsToMany(Estate::class ,'rental_contracts');
    }
    public function contracts(){
        $this->hasMany(RentalContract::class ,'tenant_id');
    }
}
