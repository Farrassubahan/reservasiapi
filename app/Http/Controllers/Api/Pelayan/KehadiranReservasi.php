<?php

namespace App\Http\Controllers\API\Pelayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;

class KehadiranReservasi extends Controller
{
    // Menampilkan semua reservasi (boleh tambahkan filter di frontend nanti)
    public function index()
    {
        $reservasi = Reservasi::with('pengguna:id,nama')
            ->orderBy('tanggal', 'desc')
            ->get(['id', 'pengguna_id', 'kode_reservasi', 'sesi', 'tanggal', 'jumlah_tamu', 'status']);

        return response()->json([
            'status' => true,
            'message' => 'Daftar reservasi',
            'data' => $reservasi
        ]);
    }

    // Konfirmasi kehadiran (ubah status)
    public function konfirmasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,dibatalkan,selesai',
        ]);

        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => 'Reservasi tidak ditemukan',
            ], 404);
        }

        $reservasi->status = $request->status;
        $reservasi->save();

        return response()->json([
            'status' => true,
            'message' => 'Status reservasi berhasil diperbarui',
            'data' => $reservasi,
        ]);
    }
}
