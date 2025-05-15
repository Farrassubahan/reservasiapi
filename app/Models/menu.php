<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu'; // Sesuai nama tabel di DB

    protected $fillable = [
        'nama',
        'kategori',
        'harga',
        'deskripsi',
        'gambar',
        'tersedia',
    ]; 
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'menu_id');
    }
}
