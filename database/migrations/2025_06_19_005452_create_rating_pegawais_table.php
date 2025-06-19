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
        Schema::create('rating_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained('reservasi')->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained('pengguna'); // pelayan atau koki
            $table->enum('tipe', ['pelayan', 'koki']);
            $table->integer('rating'); // 1-5
            $table->text('ulasan')->nullable();
            $table->timestamps();

            $table->unique(['reservasi_id', 'pegawai_id', 'tipe']); // supaya unik per reservasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_pegawais');
    }
};
