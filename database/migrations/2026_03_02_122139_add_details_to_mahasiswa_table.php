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
    Schema::table('mahasiswa', function (Blueprint $table) {
        // Tambahkan kolom yang tadi 'not found' di database
        if (!Schema::hasColumn('mahasiswa', 'nama')) {
            $table->string('nama')->after('user_id');
        }
        if (!Schema::hasColumn('mahasiswa', 'email')) {
            $table->string('email')->after('nama');
        }
        if (!Schema::hasColumn('mahasiswa', 'foto_profil')) {
            $table->string('foto_profil')->nullable()->after('email');
        }
        if (!Schema::hasColumn('mahasiswa', 'no_hp')) {
            $table->string('no_hp', 15)->nullable()->after('foto_profil');
        }
    });
}

public function down(): void
{
    Schema::table('mahasiswa', function (Blueprint $table) {
        $table->dropColumn(['nama', 'email', 'foto_profil', 'no_hp']);
    });
}
};
