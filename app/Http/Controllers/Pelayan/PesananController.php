<?php

namespace App\Http\Controllers\Pelayan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;

class PesananController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pengguna_id' => 'required|exists:pengguna,id',
            'reservasi_id' => 'nullable|exists:reservasi,id',
            'menu_id' => 'required|exists:menu,id',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
            'status' => 'nullable|in:menunggu,diproses,siap,disajikan,dibatalkan'
        ]);


        $pesanan = Pesanan::create([
            'pengguna_id' => $validated['pengguna_id'],
            'reservasi_id' => $validated['reservasi_id'] ?? null,
            'menu_id' => $validated['menu_id'],
            'jumlah' => $validated['jumlah'],
            'catatan' => $validated['catatan'] ?? null,
            'status' => $validated['status'] ?? 'menunggu',
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'data' => $pesanan
        ], 201);
    }
}
