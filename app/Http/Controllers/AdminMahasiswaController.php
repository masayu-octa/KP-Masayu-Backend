<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Divisi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\MahasiswaStatusUpdated;

class AdminMahasiswaController extends Controller
{
    public function approve($id)
    {
        return DB::transaction(function () use ($id) {

            $adminId = auth()->id();
            $mahasiswa = Mahasiswa::lockForUpdate()->findOrFail($id);

            if ($mahasiswa->status === 'diterima') {
                return response()->json(['message' => 'Sudah diterima'], 400);
            }

            // Cek kuota divisi
            $divisi = Divisi::where('nama_divisi', $mahasiswa->divisi)
                ->lockForUpdate()
                ->first();

            if (!$divisi || $divisi->sisa_kuota <= 0) {
                return response()->json(['message' => 'Kuota habis'], 400);
            }

            $divisi->decrement('sisa_kuota');

            // ✅ Ubah ke pengumuman_lolos, bukan langsung diterima
            $mahasiswa->update(['status' => 'pengumuman_lolos']);

            ActivityLog::create([
                'admin_id' => $adminId,
                'action' => 'approve',
                'mahasiswa_id' => $mahasiswa->id,
                'data' => ['divisi' => $divisi->nama_divisi]
            ]);

            event(new MahasiswaStatusUpdated($mahasiswa));

            return response()->json([
                'status' => 'success',
                'message' => 'Mahasiswa dinyatakan lolos seleksi'
            ]);
        });
    }

    public function reject($id)
    {
        return DB::transaction(function () use ($id) {

            $adminId = auth()->id();
            $mahasiswa = Mahasiswa::lockForUpdate()->findOrFail($id);

            // Jika sebelumnya sudah pengumuman_lolos, kembalikan kuota
            if ($mahasiswa->status === 'pengumuman_lolos' || $mahasiswa->status === 'diterima') {
                $divisi = Divisi::where('nama_divisi', $mahasiswa->divisi)
                    ->lockForUpdate()
                    ->first();

                if ($divisi) {
                    $divisi->increment('sisa_kuota');
                }
            }

            // ✅ Ubah ke pengumuman_tidak_lolos, bukan langsung ditolak
            $mahasiswa->update(['status' => 'pengumuman_tidak_lolos']);

            ActivityLog::create([
                'admin_id' => $adminId,
                'action' => 'reject',
                'mahasiswa_id' => $mahasiswa->id
            ]);

            event(new MahasiswaStatusUpdated($mahasiswa));

            return response()->json([
                'status' => 'success',
                'message' => 'Mahasiswa dinyatakan tidak lolos'
            ]);
        });
    }

    // ✅ Fungsi baru: Admin konfirmasi berkas sudah lengkap
    public function konfirmasiBerkas($id)
    {
        return DB::transaction(function () use ($id) {

            $adminId = auth()->id();
            $mahasiswa = Mahasiswa::lockForUpdate()->findOrFail($id);

            $mahasiswa->update(['status' => 'berkas']);

            ActivityLog::create([
                'admin_id' => $adminId,
                'action' => 'konfirmasi_berkas',
                'mahasiswa_id' => $mahasiswa->id
            ]);

            event(new MahasiswaStatusUpdated($mahasiswa));

            return response()->json([
                'status' => 'success',
                'message' => 'Berkas mahasiswa telah dikonfirmasi'
            ]);
        });
    }

    // ✅ Fungsi baru: Admin konfirmasi akhir → diterima
    public function terima($id)
    {
        return DB::transaction(function () use ($id) {

            $adminId = auth()->id();
            $mahasiswa = Mahasiswa::lockForUpdate()->findOrFail($id);

            $mahasiswa->update(['status' => 'diterima']);

            ActivityLog::create([
                'admin_id' => $adminId,
                'action' => 'terima',
                'mahasiswa_id' => $mahasiswa->id
            ]);

            event(new MahasiswaStatusUpdated($mahasiswa));

            return response()->json([
                'status' => 'success',
                'message' => 'Mahasiswa resmi diterima magang'
            ]);
        });
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $mahasiswa
        ]);
    }

    public function download($id, $jenis)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $path = storage_path('app/public/' . $mahasiswa->$jenis);

        if (!file_exists($path)) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }

        return response()->download($path);
    }

    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data mahasiswa berhasil dihapus'
        ]);
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->update($request->only([
            'nama', 'email', 'no_hp', 'nim',
            'universitas', 'jurusan', 'fakultas',
            'divisi', 'tgl_mulai', 'tgl_selesai'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Data mahasiswa berhasil diupdate',
            'data' => $mahasiswa
        ]);
    }
}