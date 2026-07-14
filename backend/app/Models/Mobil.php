<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    use HasFactory;

    protected $table = 'mobils';

    // Sesuai struktur tabel mobils yang sebenarnya:
    // id, kategori_id, nama_mobil, merk, plat_nomor, harga_sewa_per_hari, status, foto
    protected $fillable = [
        'kategori_id',
        'nama_mobil',
        'merk',
        'plat_nomor',
        'harga_sewa_per_hari',
        'status', // 'tersedia', 'servis', dst
        'foto',
    ];

    /**
     * Relasi ke kategori mobil.
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriMobil::class, 'kategori_id');
    }

    /**
     * Relasi ke booking (satu mobil bisa punya banyak booking).
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'mobil_id');
    }
}