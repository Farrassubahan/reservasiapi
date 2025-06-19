<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Jangan lupa ini
use Illuminate\Http\Request;
use App\Models\RatingPegawai;
use App\Models\Reservasi;
use App\Models\Pengguna;

class RatingPegawaiController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'reservasi_id' => 'required|exists:reservasi,id',
            'pegawai_id' => 'required|exists:pengguna,id',
            'tipe' => 'required|in:pelayan,koki',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string'
        ]);

        // Ambil data pegawai
        $pegawai = Pengguna::findOrFail($validated['pegawai_id']);

        // Validasi role pegawai sesuai tipe
        if (strtolower($pegawai->role) !== strtolower($validated['tipe'])) {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai bukan ' . $validated['tipe']
            ], 400);
        }

        // Cek apakah rating sudah ada untuk kombinasi ini
        $sudahAda = RatingPegawai::where('reservasi_id', $validated['reservasi_id'])
            ->where('pegawai_id', $validated['pegawai_id'])
            ->where('tipe', $validated['tipe'])
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'status' => false,
                'message' => 'Rating untuk pegawai ini sudah ada di reservasi ini'
            ], 400);
        }

        // Simpan rating
        $rating = RatingPegawai::create([
            'reservasi_id' => $validated['reservasi_id'],
            'pegawai_id' => $validated['pegawai_id'],
            'tipe' => $validated['tipe'],
            'rating' => $validated['rating'],
            'ulasan' => $validated['ulasan']
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Rating berhasil disimpan',
            'data' => $rating
        ]);
    }
}
