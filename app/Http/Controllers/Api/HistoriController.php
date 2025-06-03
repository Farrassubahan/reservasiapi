<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Auth;

class HistoriController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $histori = Reservasi::with(['meja', 'pesanan.menu', 'pelayan', 'ratingPelayan'])
            ->where('pengguna_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $histori
        ]);
    }
}
