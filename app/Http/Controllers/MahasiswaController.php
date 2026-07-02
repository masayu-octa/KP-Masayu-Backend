<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Divisi;

class MahasiswaController extends Controller
{
    public function registerPeserta(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama'                  => 'required|string',
            'email'                 => 'required|email',
            'nim'                   => 'required|string',
            'universitas'           => 'required|string',
            'jurusan'               => 'required|string',
            'tgl_mulai'             => 'required|date',
            'tgl_selesai'           => 'required|date',
            'divisi'                => 'required|string',
            'tipe'                  => 'required|in:individu,kelompok',
            'anggota'               => 'nullable|array',
            'anggota.*.nama'        => 'required_if:tipe,kelompok|string',
            'anggota.*.email'       => 'required_if:tipe,kelompok|email',
            'anggota.*.nim'         => 'required_if:tipe,kelompok|string',
            'anggota.*.tgl_lahir'   => 'required_if:tipe,kelompok|date',
            'anggota.*.universitas' => 'required_if:tipe,kelompok|string',
            'anggota.*.jurusan'     => 'required_if:tipe,kelompok|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        try {
            $divisi = Divisi::where('nama_divisi', $request->divisi)->first();
            if (!$divisi) {
                return response()->json(['success' => false, 'message' => 'Divisi tidak ditemukan'], 404);
            }
            if ($divisi->sisa_kuota <= 0) {
                return response()->json(['success' => false, 'message' => 'Kuota divisi sudah penuh'], 400);
            }

            DB::beginTransaction();

            // Simpan data ketua
            $mahasiswa = Mahasiswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama'          => $request->nama,
                    'email'         => $request->email,
                    'nim'           => $request->nim,
                    'universitas'   => $request->universitas,
                    'fakultas'      => $request->fakultas,
                    'jurusan'       => $request->jurusan,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'no_hp'         => $request->no_hp,
                    'instagram'     => $request->instagram,
                    'divisi'        => $request->divisi,
                    'rekomendasi'   => $request->rekomendasi,
                    'tgl_mulai'     => $request->tgl_mulai,
                    'tgl_selesai'   => $request->tgl_selesai,
                    'tipe'          => $request->tipe,
                    'status'        => 'pending',
                ]
            );

            // Buat akun anggota langsung saat daftar
            if ($request->tipe === 'kelompok' && $request->anggota) {
                DB::table('anggota_kelompok')
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->delete();

                foreach ($request->anggota as $anggotaData) {
                    // username & password = nama depan huruf kecil
                    $namaDepan = strtolower(explode(' ', trim($anggotaData['nama']))[0]);

                    $existingUser = User::where('email', $anggotaData['email'])->first();
                    if (!$existingUser) {
                        $newUser = User::create([
                            'name'     => $anggotaData['nama'],
                            'email'    => $anggotaData['email'],
                            'password' => Hash::make($namaDepan),
                            'role'     => 'mahasiswa',
                        ]);
                        $userId = $newUser->id;
                    } else {
                        $userId = $existingUser->id;
                    }

                    DB::table('anggota_kelompok')->insert([
                        'mahasiswa_id' => $mahasiswa->id,
                        'nama'         => $anggotaData['nama'],
                        'email'        => $anggotaData['email'],
                        'nim'          => $anggotaData['nim'],
                        'tgl_lahir'    => $anggotaData['tgl_lahir'],
                        'universitas'  => $anggotaData['universitas'],
                        'jurusan'      => $anggotaData['jurusan'],
                        'user_id'      => $userId,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }

            $divisi->decrement('sisa_kuota');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil',
                'data'    => $mahasiswa
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama'  => 'required|string',
            'email' => 'required|email',
            'foto'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $mahasiswa = Mahasiswa::updateOrCreate(
            ['user_id' => $user->id],
            $request->except(['foto'])
        );

        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto) {
                Storage::delete('public/fotos/' . $mahasiswa->foto);
            }
            $file     = $request->file('foto');
            $fileName = time() . '_profile_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/fotos', $fileName);
            $mahasiswa->update(['foto_profil' => $fileName]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil disimpan!',
            'data'    => $mahasiswa
        ]);
    }

    public function uploadBerkas(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'berkas_cv'              => 'required|mimes:pdf|max:5120',
            'berkas_surat_pengantar' => 'required|mimes:pdf|max:5120',
            'berkas_proposal'        => 'required|mimes:pdf|max:5120',
        ]);

        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        if (!$mahasiswa) {
            return response()->json(['message' => 'Data diri belum ada'], 404);
        }
        if ($mahasiswa->status !== 'pengumuman_lolos') {
            return response()->json(['message' => 'Anda belum berhak mengupload berkas pada tahap ini'], 403);
        }

        if ($mahasiswa->berkas_cv) Storage::delete('public/berkas/' . $mahasiswa->berkas_cv);
        $cv     = $request->file('berkas_cv');
        $cvName = time() . '_cv_' . $user->id . '.' . $cv->getClientOriginalExtension();
        $cv->storeAs('public/berkas', $cvName);

        if ($mahasiswa->berkas_surat_pengantar) Storage::delete('public/berkas/' . $mahasiswa->berkas_surat_pengantar);
        $surat     = $request->file('berkas_surat_pengantar');
        $suratName = time() . '_surat_' . $user->id . '.' . $surat->getClientOriginalExtension();
        $surat->storeAs('public/berkas', $suratName);

        if ($mahasiswa->berkas_proposal) Storage::delete('public/berkas/' . $mahasiswa->berkas_proposal);
        $proposal     = $request->file('berkas_proposal');
        $proposalName = time() . '_proposal_' . $user->id . '.' . $proposal->getClientOriginalExtension();
        $proposal->storeAs('public/berkas', $proposalName);

        $mahasiswa->update([
            'berkas_cv'              => $cvName,
            'berkas_surat_pengantar' => $suratName,
            'berkas_proposal'        => $proposalName,
            'status'                 => 'berkas',
        ]);

        return response()->json([
            'status'        => 'success',
            'message'       => 'Berkas berhasil dikirim! Menunggu konfirmasi admin.',
            'status_magang' => $mahasiswa->status,
        ]);
    }

    public function getProfile(Request $request)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if ($mahasiswa) {
            return response()->json(['status' => 'success', 'data' => $mahasiswa]);
        }
        return response()->json(['status' => 'empty', 'data' => null], 200);
    }

    public function downloadFile($folder = null, $filename = null)
    {
        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        if ($mahasiswa && $mahasiswa->status == 'diterima' && $mahasiswa->surat_balasan) {
            $path = storage_path("app/public/surat_balasan/{$mahasiswa->surat_balasan}");
            return response()->download($path);
        }

        return response()->json(['message' => 'Surat belum tersedia atau Anda belum diterima'], 403);
    }
}