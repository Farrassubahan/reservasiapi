<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    protected $table = 'meja'; 

    protected $fillable = ['nomor', 'area', 'kapasitas', 'status'];

    public function reservasi()
    {
        return $this->belongsToMany(Reservasi::class, 'reservasi_meja');
    }
}

