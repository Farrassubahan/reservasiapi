<?php
 namespace App\Models;

 use Illuminate\Database\Eloquent\Model;
 
 class Pembayaran extends Model
 {
     protected $table = 'pembayaran';
 
     protected $fillable = ['pesanan_id', 'metode', 'bukti', 'status'];
 
     public function pesanan() 
     {
         return $this->belongsTo(Pesanan::class, 'pesanan_id');
     }
 }
 