<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'mahasiswa_id',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}