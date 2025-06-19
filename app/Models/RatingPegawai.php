<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingPegawai extends Model
{
    use HasFactory;

    protected $table = 'rating_pegawai';

    protected $fillable = [
        'reservasi_id',
        'pegawai_id',
        'tipe',
        'rating',
        'ulasan'
    ];

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pengguna::class, 'pegawai_id');
    }
}
