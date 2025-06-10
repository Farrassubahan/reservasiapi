<?php

namespace App\Http\Controllers\Api\Pelayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Meja;
use App\Models\Menu;
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
            foreach ($request->pesanan as $item) {
                // Buat pesanan
                Pesanan::create([
                    'pengguna_id' => $request->pengguna_id,
                    'reservasi_id' => null, 
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? '',
                    'status' => 'menunggu'
                ]);
                Menu::where('id', $item['menu_id'])->increment('jumlah_terjual', $item['jumlah']);
            }

            // Tandai meja sebagai 'digunakan'
            Meja::where('id', $request->meja_id)->update([
                'status' => 'digunakan'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Pemesanan langsung berhasil disimpan'
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
