<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'mobil_id',
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_harga',
        'status',
    ];

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // user = pelanggan yang booking
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
