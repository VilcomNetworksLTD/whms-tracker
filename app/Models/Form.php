<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
<<<<<<< HEAD:app/Models/Form.php
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
=======
     protected $fillable = [
>>>>>>> f706664e627d7619a3ee06e7f4c021004fab3d59:app/Models/TrackerForm.php
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