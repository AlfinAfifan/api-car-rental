<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Mobil extends Model
{
    use HasFactory;

    protected $collection = 'mobil';
    protected $connection = 'mongodb';
    protected $fillable = ['merek', 'model', 'nomor_plat', 'tarif_per_hari'];
    protected $dates = ['created_at', 'updated_at'];

    public function rents() {
        return $this->hasMany(Rent::class, 'mobil_id');
    }
}
