<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // 🔥 WAJIB TAMBAH INI

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user login dan role admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        return response()->json([
            'message' => 'Akses ditolak! Anda bukan Admin.'
        ], 403);
    }
}