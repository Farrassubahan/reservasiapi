<?php

namespace App\Http\Controllers\Koki;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;

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

        $pesanan = Pesanan::findOrFail($id);
        $pesanan->status = $validated['status'];
        $pesanan->save();

        return redirect()->route('koki.pesanan')->with('success', 'Status berhasil diperbarui');
    }
}
