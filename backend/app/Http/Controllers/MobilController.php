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
                $query->where('nama_mobil', 'like', "%{$search}%")
                      ->orWhere('merk', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('mobil.index', compact('mobils'));
    }

    public function show(Mobil $mobil)
    {
        $mobil->load('kategori');

        return view('mobil.show', compact('mobil'));
    }
}
