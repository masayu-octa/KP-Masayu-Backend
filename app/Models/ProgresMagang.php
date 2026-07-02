<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresMagang extends Model
{
    protected $table = 'progres_magang';

    protected $fillable = [
        'user_id',
        'kegiatan',
        'dokumentasi',
        'status', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}