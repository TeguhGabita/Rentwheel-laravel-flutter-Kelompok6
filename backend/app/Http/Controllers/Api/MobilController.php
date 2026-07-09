<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mobil;

class MobilController extends Controller
{
    public function index()
    {
        return response()->json(Mobil::with('kategori')->get());
    }

    public function show($id)
    {
        $mobil = Mobil::with('kategori')->find($id);

        if (!$mobil) {
            return response()->json(['message' => 'Mobil tidak ditemukan'], 404);
        }

        return response()->json($mobil);
    }
}
