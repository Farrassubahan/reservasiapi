<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingPelayan extends Model
{
    protected $table = 'rating_pelayans';

    protected $fillable = ['reservasi_id', 'pelayan_id', 'rating', 'komentar', 'tanggal'];

    public function pelayan()
    {
        return $this->belongsTo(Pengguna::class, 'pelayan_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }
}
