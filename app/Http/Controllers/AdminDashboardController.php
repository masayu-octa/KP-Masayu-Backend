<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
{
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    $query = Mahasiswa::query();

    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [
            $startDate . " 00:00:00",
            $endDate . " 23:59:59"
        ]);
    }

    // cards
    $cards = [
        'pendaftar' => (clone $query)->where('status', 'pending')->count(),
        'aktif'     => (clone $query)->where('status', 'diterima')->count(),
        'instansi'  => (clone $query)->distinct('universitas')->count('universitas'),
        'nilai'     => round((clone $query)->avg('nilai') ?? 0, 1),
    ];

    // grafik pendaftaran per bulan
    $charts = (clone $query)
        ->select(
            DB::raw("DATE_FORMAT(created_at,'%b') as bulan"),
            DB::raw("count(*) as jumlah")
        )
        ->groupBy("bulan")
        ->orderByRaw("MIN(created_at)")
        ->get();

    // mahasiswa terbaru
    $recent = Mahasiswa::with("user")
        ->latest()
        ->limit(5)
        ->get();

    return response()->json([
        "cards" => $cards,
        "charts" => $charts,
        "recent_students" => $recent
    ]);
}
}