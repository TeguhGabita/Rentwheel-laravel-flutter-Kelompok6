<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriMobil;

class KategoriMobilController extends Controller
{
    public function index()
    {
        return response()->json(KategoriMobil::all());
    }
}
