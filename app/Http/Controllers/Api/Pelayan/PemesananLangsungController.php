<?php

namespace App\Http\Controllers\Api\Pelayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Reservasi;
use Illuminate\Support\Facades\DB;

class PemesananLangsungController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pengguna_id' => 'required|exists:pengguna,id',
            'meja_id' => 'required|exists:meja,id',
            'pesanan' => 'required|array|min:1',
            'pesanan.*.menu_id' => 'required|exists:menu,id',
            'pesanan.*.jumlah' => 'required|integer|min:1',
            'pesanan.*.catatan' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // 1. Tentukan sesi berdasarkan waktu sekarang
            $now = now();
            $hour = $now->format('H');

            if ($hour >= 7 && $hour < 10) {
                $sesi = 'sarapan_1';
            } elseif ($hour >= 10 && $hour < 12) {
                $sesi = 'sarapan_2';
            } elseif ($hour >= 12 && $hour < 14) {
                $sesi = 'siang_1';
            } elseif ($hour >= 14 && $hour < 17) {
                $sesi = 'siang_2';
            } elseif ($hour >= 17 && $hour < 19) {
                $sesi = 'malam_1';
            } else {
                $sesi = 'malam_2';
            }

            // 2. Buat kode reservasi acak
            $kodeReservasi = strtoupper('R' . substr(md5(uniqid()), 0, 8));

            // 3. Buat data reservasi otomatis
            $reservasi = Reservasi::create([
                'pengguna_id'    => $request->pengguna_id,
                'kode_reservasi' => $kodeReservasi,
                'sesi'           => $sesi,
                'tanggal'        => $now->toDateString(),
                'jumlah_tamu'    => 1, // default 1 (bisa ditambahkan input jika perlu)
                'status'         => 'diterima'
            ]);

            // 4. Hubungkan ke meja (pivot tabel reservasi_meja)
            $reservasi->meja()->attach($request->meja_id);

            // 5. Buat pesanan-pesanan
            foreach ($request->pesanan as $item) {
                Pesanan::create([
                    'pengguna_id'  => $request->pengguna_id,
                    'reservasi_id' => $reservasi->id,
                    'menu_id'      => $item['menu_id'],
                    'jumlah'       => $item['jumlah'],
                    'catatan'      => $item['catatan'] ?? '',
                    'status'       => 'menunggu'
                ]);

                Menu::where('id', $item['menu_id'])->increment('jumlah_terjual', $item['jumlah']);
            }

            // 6. Tandai meja sebagai 'digunakan'
            Meja::where('id', $request->meja_id)->update([
                'status' => 'digunakan'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pemesanan langsung berhasil disimpan',
                'kode_reservasi' => $kodeReservasi,
                'sesi' => $sesi,
                'reservasi_id' => $reservasi->id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
