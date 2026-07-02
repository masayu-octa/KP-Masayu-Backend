<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        $divisis = [
            ['nama_divisi' => 'Satker TIK', 'kuota_total' => 5, 'sisa_kuota' => 5],
            ['nama_divisi' => 'Satker HUMAS', 'kuota_total' => 3, 'sisa_kuota' => 0], // Sengaja di-0 kan untuk ngetes React
            ['nama_divisi' => 'Satker SDM', 'kuota_total' => 4, 'sisa_kuota' => 4],
            ['nama_divisi' => 'Satker LABFOR', 'kuota_total' => 2, 'sisa_kuota' => 2],
            ['nama_divisi' => 'Satker DITRESKRIMUM', 'kuota_total' => 5, 'sisa_kuota' => 5],
            ['nama_divisi' => 'Satker DITRESKRIMSUS', 'kuota_total' => 4, 'sisa_kuota' => 4],
            ['nama_divisi' => 'Satker BIDKUM', 'kuota_total' => 2, 'sisa_kuota' => 2],
            ['nama_divisi' => 'Satker KEU', 'kuota_total' => 3, 'sisa_kuota' => 3],
            ['nama_divisi' => 'Satker DITSIBER', 'kuota_total' => 5, 'sisa_kuota' => 5],
        ];

        foreach ($divisis as $divisi) {
            Divisi::create($divisi);
        }
    }
}