<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    protected $fillable = [
        'kategori_id',
        'nama_mobil',
        'merk',
        'plat_nomor',
        'harga_sewa_per_hari',
        'status',
        'foto',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriMobil::class, 'kategori_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
