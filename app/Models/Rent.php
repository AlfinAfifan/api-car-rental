<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'rent';
    protected $fillable = [
        'user_id', 'mobil_id', 'start_date', 'end_date', 'returned_at'
    ];

    protected $dates = ['start_date', 'end_date', 'returned_at'];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke model Mobil
    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }
}
