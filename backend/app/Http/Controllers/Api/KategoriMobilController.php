<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\KategoriMobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class KategoriMobilController extends Controller
{
    /**
     * GET /kategori-mobil
     */
    public function index()
    {
        $kategori = KategoriMobil::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $kategori,
        ]);
    }
    /**
     * GET /kategori-mobil/{id}
     */
    public function show(int $id)
    {
        $kategori = KategoriMobil::find($id);
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $kategori,
        ]);
    }
    /**
     * POST /kategori-mobil
     * Kolom asli di tabel 'kategoris' cuma 'nama_kategori' (tidak ada
     * 'nama' / 'deskripsi'), jadi validasi & create() disesuaikan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        $kategori = KategoriMobil::create([
            'nama_kategori' => $request->nama_kategori,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $kategori,
        ], 201);
    }
    /**
     * PUT /kategori-mobil/{id}
     */
    public function update(Request $request, int $id)
    {
        $kategori = KategoriMobil::find($id);
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->save();
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diupdate.',
            'data' => $kategori,
        ]);
    }
    /**
     * DELETE /kategori-mobil/{id}
     */
    public function destroy(int $id)
    {
        $kategori = KategoriMobil::find($id);
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }
        // Tolak hapus kalau masih ada mobil yang pakai kategori ini
        if ($kategori->mobils()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak bisa dihapus karena masih digunakan oleh mobil.',
            ], 409);
        }
        $kategori->delete();
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
