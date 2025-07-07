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
    
    public function statusTerbaru(Request $request)
    {
        $pengguna_id = $request->query('pengguna_id');

        if (!$pengguna_id) {
            return response()->json([
                'status' => false,
                'message' => 'pengguna_id wajib diisi'
            ], 400);
        }

        $pesanan = Pesanan::with('menu')
            ->where('pengguna_id', $pengguna_id)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get(['id', 'menu_id', 'status', 'updated_at']);

        return response()->json([
            'status' => true,
            'data' => $pesanan
        ]);
    }
}
