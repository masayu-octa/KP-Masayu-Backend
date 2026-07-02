<?php

namespace App\Http\Controllers;

use App\Models\Divisi;

class KuotaController extends Controller
{
    public function index()
    {
        $divisi = Divisi::select('id', 'nama_divisi', 'sisa_kuota')->get();

        return response()->json([
            'status' => 'success',
            'data' => $divisi
        ]);
    }

    public function adminIndex()
    {
        $divisi = Divisi::select('id', 'nama_divisi as divisi', 'sisa_kuota')->get();

        return response()->json([
            'status' => 'success',
            'data' => $divisi
        ]);
    }
}