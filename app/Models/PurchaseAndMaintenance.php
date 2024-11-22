<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseAndMaintenance extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'date',
        'quantity',
        'unit_cost',
        'total_paid',
    ];
}
