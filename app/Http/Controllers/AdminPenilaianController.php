<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class AdminPenilaianController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::where('status', 'diterima');

        if ($request->has('divisi') && $request->divisi) {
            $query->where('divisi', $request->divisi);
        }

        $data = $query->with('kelompok')->get()->map(function ($m) {
            return [
                'id'          => $m->id,
                'nama'        => $m->nama,
                'nim'         => $m->nim,
                'universitas' => $m->universitas,
                'divisi'      => $m->divisi,
                'nilai'       => $m->nilai,
                'keterangan'  => $m->keterangan,
                'kelompok'    => $m->kelompok->first()?->nama ?? 'Individu',
                'kelompok_id' => $m->kelompok->first()?->id,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::with('kelompok')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $mahasiswa
        ]);
    }

    public function hasilMagang()
    {
        $data = Mahasiswa::where('status', 'diterima')
            ->whereNotNull('nilai')
            ->get()
            ->map(function ($m) {
                return [
                    'id'          => $m->id,
                    'nama'        => $m->nama,
                    'universitas' => $m->universitas,
                    'divisi'      => $m->divisi,
                    'nilai'       => $m->nilai,
                    'keterangan'  => $m->keterangan,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nilai'      => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string'
        ]);

        $m = Mahasiswa::findOrFail($id);
        $m->update([
            'nilai'      => $request->nilai,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Penilaian diperbarui'
        ]);
    }
}