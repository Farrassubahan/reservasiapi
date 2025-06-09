<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'pengguna';

    protected $fillable = ['nama', 'email', 'telepon', 'password', 'role', 'foto', 'google_id'];

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

    public function ulasan()
    {
        // Ulasan yang ditulis pengguna ini
        return $this->hasMany(Ulasan::class, 'pengguna_id');
    }

    // Jika kamu ingin relasi ke ulasan yang menilai pengguna ini (misal ulasan untuk pengguna)
    public function ulasanDiterima()
    {
        return $this->hasMany(Ulasan::class, 'penilai_id');
    }

    public function reservasiDilayani()
    {
        return $this->hasMany(Reservasi::class, 'pelayan_id');
    }

    public function ratingPelayanDiterima()
    {
        return $this->hasMany(RatingPelayan::class, 'pelayan_id');
    }

    // Relasi ke rating koki (sebagai koki yang mendapat rating)
    public function ratingKokis()
    {
        return $this->hasMany(RatingKoki::class, 'koki_id');
    }
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            // kalau di DB cuma nama file saja
            return asset('storage/profile/' . $this->foto);
            // kalau di DB sudah lengkap "profile/namafile.jpg"
            // return asset('storage/' . $this->foto);
        }
        // kalau foto tidak ada, bisa pakai default image
        return asset('img/ajar.jpg');
    }
}
