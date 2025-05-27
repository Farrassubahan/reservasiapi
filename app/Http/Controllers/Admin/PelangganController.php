<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;

class PelangganController extends Controller
{
    // Menampilkan semua akun pelanggan (role = 'pelanggan')
    public function index()
    {
        $pelanggan = Pengguna::where('role', 'pelanggan')->get();
        return view('admin_pelanggan', compact('pelanggan'));
    }

    // Mengupdate data pelanggan berdasarkan ID
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telepon' => 'required|string|max:20',
        ]);

        $pengguna = Pengguna::findOrFail($id);

        $pengguna->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
        ]);

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    // Menghapus data pelanggan berdasarkan ID
    public function destroy($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();

        return redirect()->route('admin.pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $keyword = $request->search;

        $pelanggan = Pengguna::where('role', 'pelanggan')
            ->where('nama', 'LIKE', '%' . $keyword . '%')
            ->withCount('reservasi')
            ->orderByDesc('created_at')
            ->get();

        return view('admin_pelanggan', compact('pelanggan'));
    }
}
