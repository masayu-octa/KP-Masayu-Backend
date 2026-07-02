<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\Kelompok;

class Mahasiswa extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel (karena bukan bentuk jamak default)
    protected $table = 'mahasiswa';

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'nim',
        'universitas',
        'fakultas',
        'jurusan',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'instagram',
        'divisi',
        'rekomendasi',
        'tgl_mulai',
        'tgl_selesai',
        'status',
        'foto',
        'berkas_cv',
        'proposal_magang',
        'surat_pengantar',
        'surat_balasan',
        'nilai',
        'keterangan'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // 🔥 Relasi ke tabel users
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelompok()
    {
        return $this->belongsToMany(
            Kelompok::class,
            'kelompok_mahasiswa',
            'mahasiswa_id',
            'kelompok_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL: Helper Status Check
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'diterima';
    }

    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    public function divisiRelasi()
{
    return $this->belongsTo(Divisi::class, 'divisi', 'nama_divisi');
}
}