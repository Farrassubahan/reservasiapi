<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';

    protected $fillable = ['nama', 'email', 'password', 'role'];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    } 
}

