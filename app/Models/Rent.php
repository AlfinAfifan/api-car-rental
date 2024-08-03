<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $fillable = [
        'user_id', 'mobil_id', 'start_date', 'end_date', 'returned_at'
    ];

    protected $dates = ['start_date', 'end_date', 'returned_at'];
}
