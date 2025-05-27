<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable 
{
    use HasApiTokens, HasFactory;

    protected $table = 'pengguna';

    protected $fillable = ['nama', 'email', 'telepon', 'password','role','google_id'];

    // Relasi ke tabel reservasi
    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'pengguna_id');
    }

    // Relasi ke tabel pesanan
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'pengguna_id');
    }

    // Relasi ke tabel notifikasi
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'pengguna_id');
    }

    // Opsional: jika kamu ingin memanggil jumlah reservasi secara terpisah (misalnya untuk eager loading)
    public function getJumlahReservasiAttribute()
    {
        return $this->reservasi()->count();
    }
}
