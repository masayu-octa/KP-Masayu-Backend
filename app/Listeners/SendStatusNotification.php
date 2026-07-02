<?php

namespace App\Listeners;

use App\Events\MahasiswaStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\StatusMahasiswaMail;

class SendStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MahasiswaStatusUpdated $event)
    {
        $mahasiswa = $event->mahasiswa;

        Mail::to($mahasiswa->email ?? $mahasiswa->user->email)
            ->queue(new StatusMahasiswaMail($mahasiswa));
    }
}