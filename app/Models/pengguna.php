<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
 
class Pengguna extends Authenticatable 
{
    use HasApiTokens, HasFactory;
    protected $table = 'pengguna';

    protected $fillable = ['nama', 'email', 'telepon', 'password','role','google_id'];

    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'pengguna_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'pengguna_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'pengguna_id');
    }
}
