<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MobilRequest;
use App\Models\KategoriMobil;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    public function index(Request $request)
    {
        $mobils = Mobil::with('kategori')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_mobil', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('plat_nomor', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.mobil.index', compact('mobils'));
    }

    public function create()
    {
        $kategoris = KategoriMobil::orderBy('nama_kategori')->get();

        return view('admin.mobil.create', compact('kategoris'));
    }

    public function store(MobilRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        Mobil::create($data);

        return redirect()->route('admin.mobil.index')->with('status', 'Data mobil berhasil ditambahkan.');
    }

    public function edit(Mobil $mobil)
    {
        $kategoris = KategoriMobil::orderBy('nama_kategori')->get();

        return view('admin.mobil.edit', compact('mobil', 'kategoris'));
    }

    public function update(MobilRequest $request, Mobil $mobil)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($mobil->foto) {
                Storage::disk('public')->delete($mobil->foto);
            }
            $data['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        $mobil->update($data);

        return redirect()->route('admin.mobil.index')->with('status', 'Data mobil berhasil diperbarui.');
    }

    public function destroy(Mobil $mobil)
    {
        if ($mobil->foto) {
            Storage::disk('public')->delete($mobil->foto);
        }

        $mobil->delete();

        return redirect()->route('admin.mobil.index')->with('status', 'Data mobil berhasil dihapus.');
    }
}
