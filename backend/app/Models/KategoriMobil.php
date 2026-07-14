<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class KategoriMobil extends Model
{
    use HasFactory;
    protected $table = 'kategoris'; // nama tabel sesuai migration create_kategoris_table

    // Kolom asli di tabel 'kategoris' cuma: id, nama_kategori, created_at, updated_at
    // (dikonfirmasi lewat \App\Models\KategoriMobil::all() di tinker).
    // Sebelumnya fillable masih ['nama', 'deskripsi'] yang TIDAK cocok dengan
    // kolom asli, sehingga create()/update() dengan field 'nama_kategori'
    // ditolak diam-diam oleh mass assignment protection Laravel.
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Satu kategori bisa punya banyak mobil.
     */
    public function mobils()
    {
        return $this->hasMany(Mobil::class, 'kategori_id');
    }
}
