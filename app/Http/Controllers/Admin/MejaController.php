<?php

namespace App\Http\Controllers\Admin;

use App\Models\Meja;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class MejaController extends Controller
{
    public function index(Request $request)
    {
        $query = Meja::with(['reservasi.pengguna']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nomor', 'like', "%{$search}%");
        }

        $mejas = $query->get();

        return view('admin_meja', compact('mejas'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|unique:meja',
            'area' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:tersedia,dipesan,digunakan',
        ]);

        Meja::create($request->only('nomor', 'area', 'kapasitas', 'status'));

        return response()->json(['message' => 'Meja berhasil ditambahkan'], 201);
    }


    public function show($id)
    {
        $meja = Meja::findOrFail($id);
        return response()->json($meja);
    }

    public function update(Request $request, $id)
    {
        $meja = Meja::findOrFail($id);

        $request->validate([
            'nomor' => 'required|unique:meja,nomor,' . $meja->id,
            'area' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:tersedia,dipesan,digunakan',
        ]);

        $meja->update([
            'nomor' => $request->nomor,
            'area' => $request->area,
            'kapasitas' => $request->kapasitas,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Meja berhasil diupdate']);
    }


    public function destroy($id)
    {
        $meja = Meja::findOrFail($id);
        $meja->delete();

        return redirect()->route('meja.index')->with('success', 'Meja berhasil dihapus');
    }

    public function resetStatus(Request $request)
    {
        // Token keamanan sederhana
        if ($request->query('token') !== 'secret123') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        // Reset semua status meja menjadi 'tersedia'
        Meja::query()->update(['status' => 'tersedia']);

        // Ubah status semua reservasi hari ini yang masih 'menunggu' menjadi 'selesai'
        Reservasi::where('tanggal', Carbon::today())
            ->where('status', 'menunggu')
            ->update(['status' => 'selesai']);

        return response()->json([
            'status' => true,
            'message' => 'Status meja direset & reservasi diselesaikan.'
        ]);
    }
}
