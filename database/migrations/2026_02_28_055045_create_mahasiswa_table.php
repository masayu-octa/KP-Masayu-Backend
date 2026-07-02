<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
      ->unique()
      ->constrained()
      ->cascadeOnDelete();
            // Data Diri
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->string('instagram')->nullable();
            $table->string('foto')->nullable();

            // Data Kampus
            $table->string('universitas')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('nim', 30)->nullable();

            // Data Magang
            $table->string('divisi')->nullable();
            $table->string('rekomendasi')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('berkas_cv')->nullable();
            $table->string('berkas_surat_pengantar')->nullable();
            $table->string('berkas_proposal')->nullable();
            $table->string('proposal_magang')->nullable();
            $table->string('surat_pengantar')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');

            // Kolom Penilaian 
            $table->integer('nilai')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
