<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = [
        'pengguna_id',
        'kode_reservasi',
        'sesi',
        'tanggal',
        'jumlah_tamu',
        'status',
        'pelayan_id',
        'koki_id'
    ];

    // Relasi pengguna yang buat reservasi
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    // Relasi pelayan
    public function pelayan()
    {
        return $this->belongsTo(Pengguna::class, 'pelayan_id');
    }

    // Relasi koki
    public function koki()
    {
        return $this->belongsTo(Pengguna::class, 'koki_id');
    }

    // Relasi meja (many-to-many)
    public function meja()
    {
        return $this->belongsToMany(Meja::class, 'reservasi_meja', 'reservasi_id', 'meja_id');
    }

    // Relasi pesanan
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    // Relasi pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'reservasi_id');
    }

    // Relasi rating pelayan
    public function ratingPelayan()
    {
        return $this->hasMany(RatingPegawai::class)->where('tipe', 'pelayan');
    }

    // Relasi rating koki
    public function ratingKoki()
    {
        return $this->hasMany(RatingPegawai::class)->where('tipe', 'koki');
    }
}
