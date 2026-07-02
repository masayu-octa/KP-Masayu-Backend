<?php

namespace App\Events;

use App\Models\Mahasiswa;
use Illuminate\Foundation\Events\Dispatchable;

class MahasiswaStatusUpdated
{
    use Dispatchable;

    public $mahasiswa;

    public function __construct(Mahasiswa $mahasiswa)
    {
        $this->mahasiswa = $mahasiswa;
    }
}