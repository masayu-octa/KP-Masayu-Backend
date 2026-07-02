<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $univs = [
            'Universitas Diponegoro',
            'Universitas Gadjah Mada',
            'UNNES',
            'UDINUS',
            'UKSW'
        ];

        $divisi = [
            'Satker TIK',
            'Satker HUMAS',
            'Satker SDM',
            'Satker LABFOR',
            'Satker DITRESKRIMUM',
            'Satker DITRESKRIMSUS',
            'Satker BIDKUM',
            'Satker KEU',
            'Satker DITSIBER'
        ];

        for ($i = 1; $i <= 20; $i++) {

            $user = User::firstOrCreate(
                ['email' => 'mhs' . $i . '@test.com'],
                [
                    'name' => 'Mahasiswa Contoh ' . $i,
                    'password' => Hash::make('password123'),
                    'role' => 'user'
                ]
            );

            Mahasiswa::updateOrCreate(
    ['user_id' => $user->id],
    [
        'nama'          => $user->name,
        'email'         => $user->email,
        'tempat_lahir'  => 'Semarang',
        'tanggal_lahir' => Carbon::parse('2002-01-01')->addDays($i),
        'no_hp'         => '0812345678' . $i,
        'universitas'   => $univs[array_rand($univs)],
        'fakultas'      => 'Fakultas Ilmu Komputer',
        'jurusan'       => 'Teknik Informatika',
        'nim'           => 'A11.2022.' . rand(10000,99999),
        'divisi'        => $divisi[array_rand($divisi)],
        'rekomendasi'   => 'Universitas',
        'tgl_mulai'     => now()->subDays(rand(1,20)),
        'tgl_selesai'   => now()->addDays(rand(30,90)),
        'berkas_cv'     => 'cv_mahasiswa_' . $i . '.pdf',
        'status'        => ($i <= 10) ? 'pending' : 'diterima',
        'nilai'         => ($i > 10) ? rand(70,95) : null,
        'keterangan'    => ($i > 10) ? 'Magang berjalan dengan baik.' : null,
        'created_at'    => now()->subDays(rand(1,60)),
        'updated_at'    => now()
    ]
);
        }
    }
}