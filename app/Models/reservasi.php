<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = ['pengguna_id', 'kode_reservasi', 'sesi', 'tanggal', 'jumlah_tamu', 'status'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function meja()
    {
        return $this->belongsToMany(Meja::class, 'reservasi_meja');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}

 