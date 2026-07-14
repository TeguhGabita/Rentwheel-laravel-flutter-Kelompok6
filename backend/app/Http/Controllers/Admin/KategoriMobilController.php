<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KategoriMobilRequest;
use App\Models\KategoriMobil;
use Illuminate\Http\Request;

class KategoriMobilController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = KategoriMobil::withCount('mobils')
            ->when($request->search, function ($query, $search) {
                $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->when($request->urutan, function ($query, $urutan) {
                switch ($urutan) {
                    case 'nama_asc':
                        $query->orderBy('nama_kategori', 'asc');
                        break;
                    case 'nama_desc':
                        $query->orderBy('nama_kategori', 'desc');
                        break;
                    case 'mobil_terbanyak':
                        $query->orderBy('mobils_count', 'desc');
                        break;
                    default:
                        $query->latest();
                        break;
                }
            }, function ($query) {
                $query->latest();
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(KategoriMobilRequest $request)
    {
        KategoriMobil::create($request->validated());

        return redirect()->route('admin.kategori.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(KategoriMobil $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(KategoriMobilRequest $request, KategoriMobil $kategori)
    {
        $kategori->update($request->validated());

        return redirect()->route('admin.kategori.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriMobil $kategori)
    {
        if ($kategori->mobils()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh data mobil.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('status', 'Kategori berhasil dihapus.');
    }
}