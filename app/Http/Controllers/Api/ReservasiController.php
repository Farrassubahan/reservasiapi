<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Pesanan;
use App\Models\Pengguna;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReservasiController extends Controller
{
    public function buatReservasi(Request $request)
    {
        $request->validate([
            'pengguna_id' => 'required|exists:pengguna,id',
            'tanggal' => 'required|date',
            'sesi' => 'required|in:sarapan_1,sarapan_2,siang_1,siang_2,malam_1,malam_2',
            'jumlah_tamu' => 'required|integer|min:1',
            'pesanan' => 'required|array|min:1',
            'pesanan.*.menu_id' => 'required|exists:menu,id',
            'pesanan.*.jumlah' => 'required|integer|min:1',
            'pesanan.*.catatan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $user = Pengguna::find($request->pengguna_id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pengguna tidak ditemukan.'
                ], 404);
            }

            $kodeReservasi = strtoupper(Str::random(8));

            $reservasi = Reservasi::create([
                'pengguna_id' => $user->id,
                'kode_reservasi' => $kodeReservasi,
                'tanggal' => $request->tanggal,
                'sesi' => $request->sesi,
                'jumlah_tamu' => $request->jumlah_tamu,
                'status' => 'menunggu'
            ]);

            foreach ($request->pesanan as $item) {
                Pesanan::create([
                    'pengguna_id' => $user->id,
                    'reservasi_id' => $reservasi->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? null,
                    'status' => 'menunggu'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Reservasi berhasil dibuat.',
                'data' => $reservasi
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
   public function show($id)
{
    $reservasi = Reservasi::with(['pesanan', 'pengguna'])->find($id);
    if (!$reservasi) {
        return response()->json([
            'status' => false,
            'message' => 'Reservasi tidak ditemukan.'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $reservasi
    ]);
}

}
