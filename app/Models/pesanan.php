<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = ['pengguna_id', 'reservasi_id', 'status'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function pesananItem()
    {
        return $this->hasMany(Pesanan_Item::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}

 