<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all()->map(function ($menu) {
            $menu->gambar = basename($menu->gambar); // Ambil nama file saja tanpa path
            return $menu;
        });

        return response()->json($menus);
    }
    public function terlaris()
    {
        $menus = Menu::where('jumlah_terjual', '>', 0)
            ->orderByDesc('jumlah_terjual')
            ->take(5)
            ->get();

        foreach ($menus as $menu) {
            $menu->gambar = basename($menu->gambar);
        }

        return response()->json($menus);
    }
}
