<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswa', 'nama')) $table->string('nama')->nullable()->after('user_id');
            if (!Schema::hasColumn('mahasiswa', 'email')) $table->string('email')->nullable()->after('nama');
            if (!Schema::hasColumn('mahasiswa', 'foto_profil')) $table->string('foto_profil')->nullable()->after('email');
            if (!Schema::hasColumn('mahasiswa', 'tempat_lahir')) $table->string('tempat_lahir')->nullable()->after('foto_profil');
            if (!Schema::hasColumn('mahasiswa', 'tanggal_lahir')) $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            if (!Schema::hasColumn('mahasiswa', 'no_hp')) $table->string('no_hp', 15)->nullable()->after('tanggal_lahir');
            if (!Schema::hasColumn('mahasiswa', 'fakultas')) $table->string('fakultas')->nullable()->after('universitas');
            if (!Schema::hasColumn('mahasiswa', 'instagram')) $table->string('instagram')->nullable()->after('jurusan');
            if (!Schema::hasColumn('mahasiswa', 'divisi')) $table->string('divisi')->nullable()->after('instagram');
            if (!Schema::hasColumn('mahasiswa', 'rekomendasi')) $table->string('rekomendasi')->nullable()->after('divisi');
            if (!Schema::hasColumn('mahasiswa', 'tgl_mulai')) $table->date('tgl_mulai')->nullable()->after('rekomendasi');
            if (!Schema::hasColumn('mahasiswa', 'tgl_selesai')) $table->date('tgl_selesai')->nullable()->after('tgl_mulai');
            if (!Schema::hasColumn('mahasiswa', 'berkas_cv')) $table->string('berkas_cv')->nullable()->after('tgl_selesai');
            if (!Schema::hasColumn('mahasiswa', 'berkas_surat_pengantar')) $table->string('berkas_surat_pengantar')->nullable()->after('berkas_cv');
            if (!Schema::hasColumn('mahasiswa', 'berkas_proposal')) $table->string('berkas_proposal')->nullable()->after('berkas_surat_pengantar');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn([
                'nama', 'email', 'foto_profil', 'tempat_lahir', 'tanggal_lahir',
                'no_hp', 'fakultas', 'instagram', 'divisi', 'rekomendasi',
                'tgl_mulai', 'tgl_selesai', 'berkas_cv', 'berkas_surat_pengantar', 'berkas_proposal'
            ]);
        });
    }
};