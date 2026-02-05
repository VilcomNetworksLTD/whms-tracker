<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
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

    public function user()
    {
        return $this->belongsTo(Domain::class);
    }
}