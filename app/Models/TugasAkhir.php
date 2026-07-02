<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasAkhir extends Model
{
    protected $fillable = [
        'user_id',
        'divisi',
        'file_tugas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
