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

        $histori->transform(function ($reservasi) {
            $totalHarga = 0;
            foreach ($reservasi->pesanan as $pesanan) {
                $hargaMenu = $pesanan->menu->harga ?? 0;
                $jumlahPesanan = $pesanan->jumlah ?? 1;
                $totalHarga += $hargaMenu * $jumlahPesanan;
            }
            $reservasi->total_harga = $totalHarga;
            return $reservasi;
        });

        return response()->json([
            'status' => 'success',
            'data' => $histori
        ]);
    }
}
