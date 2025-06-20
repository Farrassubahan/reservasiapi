<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = ['pengguna_id', 'judul', 'pesan', 'dibaca'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'penggunaz_id');
    }
}

