<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingKoki extends Model
{
    protected $table = 'rating_kokis';

    protected $fillable = ['pesanan_id', 'koki_id', 'rating', 'komentar', 'tanggal'];

    public function koki()
    {
        return $this->belongsTo(Pengguna::class, 'koki_id');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
