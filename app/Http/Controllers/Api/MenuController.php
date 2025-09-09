<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller     
{
    public function index()
    {
        $menus = Menu::where('status', 'tersedia') // ✅ Hanya ambil menu dengan status "tersedia"
            ->get()
            ->map(function ($menu) {
                $menu->gambar = basename($menu->gambar); // Ambil nama file gambar saja
                return $menu;
            });

        return response()->json($menus);
    }

    public function terlaris()
    {
        $menus = Menu::where('jumlah_terjual', '>', 0)
            ->where('status', 'tersedia') // ✅ Pastikan statusnya "tersedia"
            ->orderByDesc('jumlah_terjual')
            ->take(5)
            ->get();

        foreach ($menus as $menu) {
            $menu->gambar = basename($menu->gambar);
        }

        return response()->json($menus);
    }
}
