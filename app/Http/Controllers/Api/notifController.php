<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Pesanan;
use App\Models\Notifikasi;

class notifController extends Controller
{
    // ✅ Update status reservasi dan kirim notifikasi jika berubah
    public function updateReservasiStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $reservasi = Reservasi::findOrFail($id);

        $statusLama = $reservasi->status;
        $statusBaru = $request->input('status');

        if ($statusLama !== $statusBaru) {
            $reservasi->status = $statusBaru;
            $reservasi->save();

            Notifikasi::create([
                'pengguna_id' => $reservasi->pengguna_id,
                'judul' => 'Status Reservasi Berubah',
                'pesan' => 'Status reservasi Anda berubah dari "' . $statusLama . '" menjadi "' . $statusBaru . '".',
                'tipe' => 'status_reservasi',
                'status' => $statusBaru,
                'dibaca' => false
            ]);
        }

        return response()->json([
            'message' => 'Status reservasi diperbarui.',
            'data' => $reservasi
        ]);
    }

    // ✅ Update status pesanan dan kirim notifikasi (oleh koki)
    public function updatePesananStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $pesanan = Pesanan::findOrFail($id);

        $statusLama = $pesanan->status;
        $statusBaru = $request->input('status');

        if ($statusLama !== $statusBaru) {
            $pesanan->status = $statusBaru;
            $pesanan->save();

            // Di updatePesananStatus
            Notifikasi::create([
                'pengguna_id' => $pesanan->pengguna_id,
                'judul' => 'Status Pesanan Berubah',
                'pesan' => 'Status pesanan Anda berubah dari "' . $statusLama . '" menjadi "' . $statusBaru . '".',
                'tipe' => 'status_pesanan',
                'status' => $statusBaru, // wajib supaya terbaca di frontend
                'dibaca' => false
            ]);
        }

        return response()->json([
            'message' => 'Status pesanan diperbarui.',
            'data' => $pesanan
        ]);
    }

    // // ✅ Ambil notifikasi milik user (untuk user di aplikasi Ionic)
    // public function getNotifikasi(Request $request)
    // {
    //     $userId = $request->user()->id ?? $request->pengguna_id;

    //     $notifikasi = Notifikasi::where('pengguna_id', $userId)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return response()->json($notifikasi);
    // }

    // ✅ Tandai notifikasi sudah dibaca
    // public function tandaiDibaca($id)
    // {
    //     $notifikasi = Notifikasi::findOrFail($id);
    //     $notifikasi->update(['dibaca' => true]);

    //     return response()->json(['message' => 'Notifikasi ditandai sebagai dibaca']);
    // }
    // public function cekStatusPesananTerbaru(Request $request)
    // {
    //     $pengguna_id = $request->query('pengguna_id');

    //     $pesanan = \App\Models\Pesanan::where('pengguna_id', $pengguna_id)
    //         ->orderBy('updated_at', 'desc')
    //         ->take(5) // ambil 5 terakhir
    //         ->get(['id', 'menu_id', 'status', 'updated_at']);

    //     return response()->json([
    //         'status' => true,
    //         'data' => $pesanan
    //     ]);
    // }


// yang baru
    public function getNotifikasiByToken(Request $request)
    {
        $user = $request->user(); // Ambil user dari token

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan dari token.'
            ], 401);
        }

        $notifikasi = Notifikasi::where('pengguna_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notifikasi
        ]);
    }

    public function tandaiDibaca($id)
    {
        $notifikasi = Notifikasi::findOrFail($id);

        $notifikasi->update(['dibaca' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Notifikasi berhasil ditandai sebagai dibaca.'
        ]);
    }
}
