<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mobil;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori'];

    public function mobils()
    {
        return $this->hasMany(Mobil::class);
    }
}
