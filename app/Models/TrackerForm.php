<?php
// app/Models/TrackerForm.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackerForm extends Model
{
    protected $fillable = [
        'user_id',
        'client_name',
        'date',
        'payment_method',
        'description',
        'amount_in',
        'fees',
        'amount_out',
        'feedback',
        'feedback_date',
        'sales_person'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'feedback_date' => 'date:Y-m-d',
        'amount_in' => 'decimal:2',
        'fees' => 'decimal:2',
        'amount_out' => 'decimal:2',
    ];

    /**
     * Get the user that owns the tracker form.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}