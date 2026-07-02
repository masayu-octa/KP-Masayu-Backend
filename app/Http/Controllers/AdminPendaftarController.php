<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Kelompok;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPendaftarController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $mahasiswa
        ]);
    }

    public function approve($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->update(['status' => 'pengumuman_lolos']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Mahasiswa berhasil disetujui, menunggu upload berkas'
        ]);
    }

    public function reject($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->update(['status' => 'pengumuman_tidak_lolos']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Mahasiswa dinyatakan tidak lolos'
        ]);
    }

    public function konfirmasiBerkas($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->update(['status' => 'berkas']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Berkas mahasiswa telah dikonfirmasi'
        ]);
    }

    public function terima($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        DB::beginTransaction();
        try {
            $mahasiswa->update(['status' => 'diterima']);

            $anggotaIds = [$mahasiswa->id];

            if ($mahasiswa->tipe === 'kelompok') {
                $anggotaList = DB::table('anggota_kelompok')
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->get();

                foreach ($anggotaList as $a) {
                    // Akun sudah dibuat saat daftar, ambil saja
                    $userAnggota = User::where('email', $a->email)->first();

                    if (!$userAnggota) {
                        // Fallback jika akun belum ada
                        $namaDepan   = strtolower(explode(' ', trim($a->nama))[0]);
                        $userAnggota = User::create([
                            'name'     => $a->nama,
                            'email'    => $a->email,
                            'password' => Hash::make($namaDepan),
                            'role'     => 'mahasiswa',
                        ]);
                        DB::table('anggota_kelompok')
                            ->where('id', $a->id)
                            ->update(['user_id' => $userAnggota->id]);
                    }

                    // Buat record mahasiswa untuk anggota jika belum ada
                    $mahasiswaAnggota = Mahasiswa::withTrashed()
                        ->where('user_id', $userAnggota->id)
                        ->first();

                    if (!$mahasiswaAnggota) {
                        $mahasiswaAnggota = Mahasiswa::create([
                            'user_id'     => $userAnggota->id,
                            'nama'        => $a->nama,
                            'email'       => $a->email,
                            'nim'         => $a->nim,
                            'universitas' => $a->universitas,
                            'jurusan'     => $a->jurusan,
                            'divisi'      => $mahasiswa->divisi,
                            'tgl_mulai'   => $mahasiswa->tgl_mulai,
                            'tgl_selesai' => $mahasiswa->tgl_selesai,
                            'tipe'        => 'kelompok',
                            'status'      => 'diterima',
                        ]);
                    } else {
                        $mahasiswaAnggota->restore(); // kalau soft deleted
                        $mahasiswaAnggota->update(['status' => 'diterima']);
                    }

                    $anggotaIds[] = $mahasiswaAnggota->id;
                }

                $kelompok = Kelompok::create([
                    'nama'   => 'Kelompok ' . $mahasiswa->nama,
                    'tipe'   => 'kelompok',
                    'divisi' => $mahasiswa->divisi,
                ]);
            } else {
                $kelompok = Kelompok::create([
                    'nama'   => $mahasiswa->nama,
                    'tipe'   => 'individu',
                    'divisi' => $mahasiswa->divisi,
                ]);
            }

            $kelompok->mahasiswa()->attach($anggotaIds);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Mahasiswa resmi diterima magang.' .
                    ($mahasiswa->tipe === 'kelompok'
                        ? ' ' . count($anggotaIds) . ' anggota berhasil ditambahkan.'
                        : ''),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menerima: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'data'   => $mahasiswa
        ]);
    }
}