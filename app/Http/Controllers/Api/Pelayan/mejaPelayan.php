<?php

namespace App\Http\Controllers\API\Pelayan;

use App\Http\Controllers\Controller;
use App\Models\Meja;  
use Illuminate\Http\Request;

class mejaPelayan extends Controller
{
    public function index()
    {
        // Ambil semua data meja
        $meja = Meja::select('id', 'nomor', 'area', 'kapasitas', 'status', 'created_at', 'updated_at')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data semua meja berhasil diambil',
            'data' => $meja
        ]);
    }
}
