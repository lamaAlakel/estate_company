<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable=[
        'estate_id',
        'meter_number',
        'account_number',
        'total_invoice_amount',
        'type',
        'date'
    ];
    public function payments(){
        return $this->hasMany(InvoicePayment::class ,'invoice_id');
    }
    public function estate(){
        return $this->belongsTo(Estate::class , 'estate_id');
    }

}
