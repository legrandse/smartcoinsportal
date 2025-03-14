<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    //use HasFactory;
    
     
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'status',
        'amount',
        'inserted_amount',
        'reference',
        'debtor',
        
    ];
}
