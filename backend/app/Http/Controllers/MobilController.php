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

    public function create()
    {
        $kategoris = \App\Models\KategoriMobil::all();
        return view('mobil.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_mobil' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'plat_nomor' => 'required|string|max:50|unique:mobils,plat_nomor',
            'harga_sewa_per_hari' => 'required|numeric',
            'status' => 'required|in:tersedia,disewa,servis',
        ]);

        Mobil::create($data);

        return redirect()->route('mobil.index')->with('status', 'Mobil berhasil ditambahkan.');
    }

    public function edit(Mobil $mobil)
    {
        $kategoris = \App\Models\KategoriMobil::all();
        return view('mobil.edit', compact('mobil', 'kategoris'));
    }

    public function update(Request $request, Mobil $mobil)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_mobil' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'plat_nomor' => "required|string|max:50|unique:mobils,plat_nomor,{$mobil->id}",
            'harga_sewa_per_hari' => 'required|numeric',
            'status' => 'required|in:tersedia,disewa,servis',
        ]);

        $mobil->update($data);

        return redirect()->route('mobil.index')->with('status', 'Mobil berhasil diperbarui.');
    }

    public function destroy(Mobil $mobil)
    {
        $mobil->delete();

        return redirect()->route('mobil.index')->with('status', 'Mobil berhasil dihapus.');
    }

    public function show(Mobil $mobil)
    {
        $mobil->load('kategori');

        return view('mobil.show', compact('mobil'));
    }
}
