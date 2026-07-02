<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class AdminKelompokController extends Controller
{
    // Ambil semua kelompok per divisi
    public function index(Request $request)
    {
        $divisi = $request->query('divisi');

        $kelompok = Kelompok::with(['mahasiswa' => function ($q) {
                $q->select('mahasiswa.id', 'nama', 'nim', 'universitas', 'divisi', 'nilai', 'keterangan');
            }])
            ->where('divisi', $divisi)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $kelompok
        ]);
    }

    // Buat kelompok baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'tipe' => 'required|in:kelompok,individu',
            'divisi' => 'required|string',
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
        ]);

        $kelompok = Kelompok::create([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'divisi' => $request->divisi,
        ]);

        $kelompok->mahasiswa()->attach($request->mahasiswa_ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Kelompok berhasil dibuat',
            'data' => $kelompok->load('mahasiswa')
        ]);
    }

    // Hapus kelompok
    public function destroy($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        $kelompok->mahasiswa()->detach();
        $kelompok->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kelompok berhasil dihapus'
        ]);
    }

    // Update nama kelompok
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string',
        ]);

        $kelompok = Kelompok::findOrFail($id);
        $kelompok->update(['nama' => $request->nama]);

        return response()->json([
            'status' => 'success',
            'message' => 'Nama kelompok berhasil diupdate',
            'data' => $kelompok
        ]);
    }
}