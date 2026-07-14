<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;

class MobilController extends Controller
{
    public function index(Request $request)
    {
        $mobils = Mobil::with('kategori')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_mobil', 'like', "%{$search}%")
                      ->orWhere('merk', 'like', "%{$search}%");
                });
            })
            ->when($request->merk, function ($query, $merk) {
                $query->where('merk', $merk);
            })
            ->when($request->harga_min, function ($query, $hargaMin) {
                $query->where('harga_sewa_per_hari', '>=', $hargaMin);
            })
            ->when($request->harga_max, function ($query, $hargaMax) {
                $query->where('harga_sewa_per_hari', '<=', $hargaMax);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        // Untuk dropdown pilihan merk (ambil daftar merk unik yang ada)
        $daftarMerk = Mobil::select('merk')->distinct()->orderBy('merk')->pluck('merk');

        return view('mobil.index', compact('mobils', 'daftarMerk'));
    }

    public function show(Mobil $mobil)
    {
        $mobil->load('kategori');

        return view('mobil.show', compact('mobil'));
    }
}
