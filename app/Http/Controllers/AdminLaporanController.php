<?php

namespace App\Http\Controllers;

class AdminLaporanController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Data laporan'
        ]);
    }
}