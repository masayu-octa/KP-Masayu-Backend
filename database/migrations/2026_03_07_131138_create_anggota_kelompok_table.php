<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_kelompok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('nim');
            $table->date('tgl_lahir');
            $table->string('universitas');
            $table->string('jurusan');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->enum('tipe', ['individu', 'kelompok'])->default('individu')->after('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_kelompok');
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};