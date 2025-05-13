<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
 
// class MejaController extends Controller
// {
//     //
// }

// app/Http/Controllers/Admin/MejaController.php
namespace App\Http\Controllers\Admin;

use App\Models\Meja;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MejaController extends Controller
{
    // Menampilkan semua meja
    public function index()
    {
        $mejas = Meja::all();
        return response()->json($mejas);
    }

    // Menambahkan meja baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nomor' => 'required|unique:meja',
            'area' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:tersedia,dipesan,digunakan',
        ]);

        // Membuat meja baru
        $meja = Meja::create([
            'nomor' => $request->nomor,
            'area' => $request->area,
            'kapasitas' => $request->kapasitas,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Meja berhasil ditambahkan',
            'meja' => $meja
        ], 201);
    }

    // Menampilkan meja berdasarkan ID
    public function show($id)
    {
        $meja = Meja::findOrFail($id);
        return response()->json($meja);
    }

    // Mengupdate meja berdasarkan ID
    public function update(Request $request, $id)
    {
        $meja = Meja::findOrFail($id);

        // Validasi input
        $request->validate([
            'nomor' => 'required|unique:meja,nomor,' . $meja->id,
            'area' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:tersedia,dipesan,digunakan',
        ]);

        // Mengupdate meja
        $meja->update([
            'nomor' => $request->nomor,
            'area' => $request->area,
            'kapasitas' => $request->kapasitas,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Meja berhasil diupdate',
            'meja' => $meja
        ]);
    }

    // Menghapus meja berdasarkan ID
    public function destroy($id)
    {
        $meja = Meja::findOrFail($id);
        $meja->delete();

        return response()->json([
            'message' => 'Meja berhasil dihapus'
        ]);
    }
}
