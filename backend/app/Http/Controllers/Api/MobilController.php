<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Mobil;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_mobil' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:255',
            'harga_sewa_per_hari' => 'required|numeric',
            'status' => 'required|string',
            'foto' => 'nullable|string',
        ]);

        $mobil = Mobil::create($validated);

        return response()->json([
            'message' => 'Mobil berhasil ditambahkan',
            'data' => $mobil,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $mobil = Mobil::find($id);
        if (!$mobil) {
            return response()->json(['message' => 'Mobil tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'kategori_id' => 'sometimes|required|exists:kategoris,id',
            'nama_mobil' => 'sometimes|required|string|max:255',
            'merk' => 'sometimes|required|string|max:255',
            'plat_nomor' => 'sometimes|required|string|max:255',
            'harga_sewa_per_hari' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|string',
            'foto' => 'nullable|string',
        ]);

        $mobil->update($validated);

        return response()->json([
            'message' => 'Mobil berhasil diupdate',
            'data' => $mobil,
        ]);
    }

    public function destroy($id)
    {
        $mobil = Mobil::find($id);
        if (!$mobil) {
            return response()->json(['message' => 'Mobil tidak ditemukan'], 404);
        }

        $mobil->delete();

        return response()->json(['message' => 'Mobil berhasil dihapus']);
    }
}
