<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory;

    protected $table = 'ulasan';

    protected $fillable = [
        'pesanan_id',
        'pengguna_id',
        'rating',
        'komentar',
    ];

    // Relasi opsional
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function pengguna()
    {
        // Pengguna yang membuat ulasan
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
