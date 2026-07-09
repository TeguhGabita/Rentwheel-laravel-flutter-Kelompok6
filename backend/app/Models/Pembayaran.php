<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'booking_id',
        'tanggal_bayar',
        'metode_bayar',
        'jumlah_bayar',
        'status_bayar',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
