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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            // Menyambungkan dengan ID User
            $table->unsignedBigInteger('user_id'); 
            
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable(); // Boleh kosong saat baru masuk
            $table->time('jam_pulang')->nullable(); // Boleh kosong jika belum pulang
            $table->string('status')->default('Hadir'); // Otomatis terisi "Hadir"
            
            $table->timestamps(); // Membuat kolom created_at dan updated_at
            
            // Opsional: Jika user dihapus, data absennya ikut terhapus
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
