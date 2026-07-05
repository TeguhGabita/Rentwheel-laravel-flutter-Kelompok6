<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriMobil extends Model
{
    protected $table = 'kategoris'; // tambahkan baris ini

    protected $fillable = ['nama_kategori'];

    public function mobils()
    {
        return $this->hasMany(Mobil::class, 'kategori_id');
    }
}
