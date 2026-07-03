<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'no_ktp',
        'no_hp',
        'alamat',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
