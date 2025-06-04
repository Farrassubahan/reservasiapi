<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = ['reservasi_id', 'metode', 'bukti', 'status'];

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }
}
