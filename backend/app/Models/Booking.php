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
        'metode_pembayaran'
    ];

    /**
     * Relasi ke mobil
     */
    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Satu booking memiliki satu pembayaran
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    /**
     * Cek apakah booking sudah dibayar
     */
    public function sudahDibayar(): bool
    {
        return $this->pembayaran !== null;
    }

    /**
     * Cek apakah booking belum dibayar
     */
    public function belumDibayar(): bool
    {
        return $this->pembayaran === null;
    }
}
