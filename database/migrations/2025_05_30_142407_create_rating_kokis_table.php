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
        Schema::create('rating_kokis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade');
            $table->foreignId('koki_id')->constrained('pengguna')->onDelete('cascade');
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
        Schema::dropIfExists('rating_kokis');
    }
};
