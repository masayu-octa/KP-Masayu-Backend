<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = Mahasiswa::with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($p) {
                return [
                    'title' => 'Pendaftar Baru',
                    'message' => ($p->user->name ?? 'Seseorang') .
                        " dari " . $p->universitas . " telah mendaftar",
                    'time' => $p->created_at->diffForHumans(),
                    'id' => $p->id
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }
}