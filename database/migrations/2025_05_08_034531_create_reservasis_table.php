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
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('cascade');
            $table->string('kode_reservasi')->unique();
            $table->enum('sesi', ['sarapan_1', 'sarapan_2', 'siang_1', 'siang_2', 'malam_1', 'malam_2']);
            $table->date('tanggal');
            $table->integer('jumlah_tamu');
            $table->enum('status', ['menunggu', 'diterima', 'dibatalkan', 'selesai'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. 
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasis'); 
    }
};
