<?php

namespace App\Http\Controllers\Koki;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\RatingKoki;
use Illuminate\Support\Facades\Auth;

class DapurController extends Controller
{
    public function index()
    {
        $pesanan = Pesanan::with(['menu', 'pengguna'])
            ->whereNotIn('status', ['disajikan', 'dibatalkan'])
            ->orderBy('created_at')
            ->get();

        return view('koki_dashboard', compact('pesanan'));
    }

    public function pesananMasuk()
    {
        $pesanan = Pesanan::with(['menu', 'pengguna'])
            ->whereNotIn('status', ['disajikan', 'dibatalkan'])
            ->orderBy('created_at')
            ->get();

        return view('koki_pesanan', compact('pesanan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,diproses,siap,disajikan,dibatalkan'
        ]);

        $pesanan = Pesanan::with('reservasi')->findOrFail($id);
        $pesanan->status = $validated['status'];
        $pesanan->save();

        // Simpan koki_id di reservasi kalau belum ada
        if (is_null($pesanan->reservasi->koki_id)) {
            $pesanan->reservasi->koki_id = Auth::id();
            $pesanan->reservasi->save();
        }

        return redirect()->route('koki.pesanan')->with('success', 'Status berhasil diperbarui');
    }
}
