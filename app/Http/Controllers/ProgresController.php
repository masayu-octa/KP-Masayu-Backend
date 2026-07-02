<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgresMagang; // Diperbaiki: Models (pakai s)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgresController extends Controller // Pastikan nama class-nya ProgresController (satu s)
{
    // Fungsi untuk menarik data logbook mahasiswa yang login
    public function index()
    {
        $user = Auth::user();
        $progres = ProgresMagang::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progres
        ]);
    }

    // Fungsi untuk menyimpan kegiatan baru
    public function store(Request $request) 
    {
        $request->validate([
            'kegiatan' => 'required|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048' // Maks 2MB
        ]);

        $fileName = null;

        if ($request->hasFile('dokumentasi')) {
            $file = $request->file('dokumentasi');
            // Beri nama unik agar tidak tertimpa
            $fileName = time() . '_progres_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            // Simpan ke folder public/progres
            $file->storeAs('public/progres', $fileName);
        }

        $progres = ProgresMagang::create([
            'user_id' => Auth::id(),
            'kegiatan' => $request->kegiatan,
            'dokumentasi' => $fileName,
            'status' => 'Pending' // Status default
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progres berhasil disimpan',
            'data' => $progres
        ]);
    }
}