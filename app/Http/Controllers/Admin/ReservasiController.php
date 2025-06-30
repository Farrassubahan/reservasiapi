<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Pengguna;

class ReservasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservasi::with(['pengguna', 'pembayaran']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('pengguna', function ($sub) use ($search) {
                    $sub->where('nama', 'like', '%' . $search . '%');
                })->orWhere('kode_reservasi', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservasi = $query->latest()->get();

        return view('admin_reservasi', compact('reservasi'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diterima,dibatalkan,selesai',
        ]);

        $reservasi = Reservasi::findOrFail($id);
        $reservasi->status = $request->status;
        $reservasi->save();

        // ✅ Jika status selesai, ubah semua meja yang terkait jadi 'tersedia'
        if ($request->status === 'selesai') {
            foreach ($reservasi->meja as $meja) {
                $meja->status = 'tersedia';
                $meja->save();
            }
        }

        return redirect()->back()->with('success', 'Status reservasi diperbarui.');
    }


    public function destroy($id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->delete();

        return redirect()->back()->with('success', 'Reservasi berhasil dihapus.');
    }
}
