<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('admin_createmenu', compact('menus'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image',
        ]);
        
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('gambar_menu', 'public');
            $validated['gambar'] = $path;
        }
        
        // $stokInput = (int) $request->input('stok');
        // $validated['tersedia'] = $stokInput > 0 ? 'tersedia' : 'kosong';
        $validated['tersedia'] = 'tersedia';
        
        Menu::create($validated);
        
        return response()->json([
            'message' => 'Menu berhasil ditambahkan.'
        ]);
    }
    
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        return response()->json($menu);
    }
    
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|image',
            'deskripsi' => 'required|string',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }

            // Simpan gambar baru
            $path = $request->file('gambar')->store('gambar_menu', 'public');
            $validated['gambar'] = $path;
        }

        $menu->update($validated);

        return response()->json(['message' => 'Menu berhasil diperbarui.']);
    }



    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
            Storage::disk('public')->delete($menu->gambar);
        }

        $menu->delete();

        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }

    public function ubahStok(Request $request, $id)
    {
        $request->validate([
            'tersedia' => 'required|in:tersedia,kosong'
        ]);

        $menu = Menu::findOrFail($id);
        $menu->tersedia = $request->tersedia;
        $menu->save();

        return back()->with('success', 'Status stok berhasil diperbarui.');
    }
}
