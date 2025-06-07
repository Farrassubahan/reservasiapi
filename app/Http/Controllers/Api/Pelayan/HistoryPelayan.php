<?php

namespace App\Http\Controllers\API\Pelayan;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;

class HistoryPelayan extends Controller
{
    public function index()
    {
        
        $reservasi = Reservasi::with('meja:id,nomor') 
            ->whereIn('status', ['menunggu','diterima','dibatalkan','selesai']) 
            ->orderBy('tanggal', 'desc')
            ->get(['id', 'pengguna_id', 'kode_reservasi', 'sesi', 'tanggal', 'jumlah_tamu', 'status']);

        return response()->json([
            'status' => true,
            'message' => 'Riwayat reservasi untuk pelayan',
            'data' => $reservasi,
        ]);
    }
}
