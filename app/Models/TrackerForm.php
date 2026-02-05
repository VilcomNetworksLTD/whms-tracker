<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackerForm extends Model
{
     protected $fillable = [
        'client_name',
        'date',
        'payment_method',
        'description',
        'amount_in',
        'fees',
        'amount_out',
        'feedback',
        'feedback_date'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',         
        'feedback_date' => 'date:Y-m-d', 
        'amount_in' => 'decimal:2',      
        'fees' => 'decimal:2',          
        'amount_out' => 'decimal:2',     
    ];
}
