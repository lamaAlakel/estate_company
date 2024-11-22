<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;
    protected $fillable=[
        'amount',
        'invoice_id',
        'date'
    ];
    public function invoice(){
        return $this->belongsTo(Invoice::class ,'invoice_id');
    }
}
