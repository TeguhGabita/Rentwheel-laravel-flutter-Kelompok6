<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'booking_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_bayar',
        'status_bayar',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
