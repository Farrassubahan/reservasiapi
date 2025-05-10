<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = ['nama', 'kategori', 'harga', 'deskripsi', 'gambar', 'tersedia'];

    public function pesananItem()
    {
        return $this->hasMany(PesananItem::class);
    }
}
