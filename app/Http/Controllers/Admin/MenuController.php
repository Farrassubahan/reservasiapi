<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return response()->json($menus);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman',
            'harga' => 'required|numeric',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|string',
            'tersedia' => 'required|in:tersedia,penuh',
        ]);

        $menu = Menu::create($validated);

        return response()->json(['message' => 'Menu berhasil ditambahkan', 'data' => $menu], 201);
    }

    public function show($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman',
            'harga' => 'required|numeric',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|string',
            'tersedia' => 'required|in:tersedia,penuh',
        ]);

        $menu->update($validated);

        return response()->json(['message' => 'Menu berhasil diperbarui', 'data' => $menu]);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu berhasil dihapus']);
    }
}
