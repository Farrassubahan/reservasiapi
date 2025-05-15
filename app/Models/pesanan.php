<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'pengguna_id',
        'reservasi_id',
        'status',
        'menu_id',
        'jumlah',
        'catatan',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function reservasi()
    { 
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
    
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
