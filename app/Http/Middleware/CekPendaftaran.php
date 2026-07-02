<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CekPendaftaran
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user->mahasiswa) {
            return response()->json([
                'message' => 'Anda belum mendaftar magang'
            ], 403);
        }

        return $next($request);
    }
}