<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    protected $table = 'kelompok';

    protected $fillable = ['nama', 'tipe', 'divisi'];

    public function mahasiswa()
    {
        return $this->belongsToMany(
            Mahasiswa::class,
            'kelompok_mahasiswa',
            'kelompok_id',
            'mahasiswa_id'
        );
    }
}