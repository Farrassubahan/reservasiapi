<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rating_pelayans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained('reservasi')->onDelete('cascade');
            $table->foreignId('pelayan_id')->constrained('pengguna')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('1 = buruk, 5 = sangat baik');
            $table->text('komentar')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_pelayans');
    }
};
