<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Mahasiswa;

class AbsensiController extends Controller
{
    /**
     * 🔥 PROTEKSI STATUS MAGANG
     * Hanya mahasiswa dengan status 'diterima' yang boleh akses fitur magang
     */
    private function checkStatusMagang($user)
    {
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        if (!$mahasiswa || $mahasiswa->status !== 'diterima') {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum resmi diterima sebagai peserta magang.'
            ], 403);
        }

        return null;
    }

    /**
     * VERIFIKASI WAJAH + ABSEN MASUK/PULANG
     */
    public function verifyFace(Request $request)
    {
        $user = Auth::user();

        // 🔒 Cek status dulu
        if ($response = $this->checkStatusMagang($user)) {
            return $response;
        }

        // Validasi input
        $request->validate([
            'image' => 'required|string'
        ]);

        try {

            // ===============================
            // 🔥 OPTIONAL: VERIFIKASI KE PYTHON AI
            // ===============================
            /*
            $pythonResponse = Http::post('http://127.0.0.1:5000/verify-face', [
                'user_id' => $user->id,
                'image' => $request->image
            ]);

            if (!$pythonResponse->successful() || !$pythonResponse->json('success')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak dikenali atau tidak cocok!'
                ], 400);
            }
            */
            // ===============================


            $hariIni = Carbon::today()->toDateString();
            $sekarang = Carbon::now()->toTimeString();

            $absenHariIni = DB::table('absensi')
                ->where('user_id', $user->id)
                ->where('tanggal', $hariIni)
                ->first();

            if (!$absenHariIni) {

                // ✅ ABSEN MASUK
                DB::table('absensi')->insert([
                    'user_id' => $user->id,
                    'tanggal' => $hariIni,
                    'jam_masuk' => $sekarang,
                    'status' => 'Hadir',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $pesan = "Absen Masuk Berhasil pada $sekarang";

            } else if ($absenHariIni && null === $absenHariIni->jam_pulang) {

                // ✅ ABSEN PULANG
                DB::table('absensi')
                    ->where('id', $absenHariIni->id)
                    ->update([
                        'jam_pulang' => $sekarang,
                        'updated_at' => now()
                    ]);

                $pesan = "Absen Pulang Berhasil pada $sekarang";

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan Absen Masuk dan Pulang hari ini.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $pesan
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan di server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CEK ABSEN HARI INI
     */
    public function cekAbsenHariIni(Request $request)
    {
        $user = Auth::user();

        // 🔒 Cek status dulu
        if ($response = $this->checkStatusMagang($user)) {
            return $response;
        }

        $hariIni = Carbon::today()->toDateString();

        $absen = DB::table('absensi')
            ->where('user_id', $user->id)
            ->where('tanggal', $hariIni)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $absen
        ]);
    }

    /**
     * RIWAYAT ABSENSI
     */
    public function getRiwayat(Request $request)
    {
        $user = Auth::user();

        // 🔒 Cek status dulu
        if ($response = $this->checkStatusMagang($user)) {
            return $response;
        }

        $riwayat = DB::table('absensi')
            ->where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $riwayat
        ]);
    }
}