<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
            'kategori' => 'required|in:makanan,minuman,snack',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('gambar_menu');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $validated['gambar'] = 'gambar_menu/' . $filename; // simpan path relatif
        }

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
            'kategori' => 'required|in:makanan,minuman,snack',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama di folder public/gambar_menu
            if ($menu->gambar && file_exists(public_path($menu->gambar))) {
                File::delete(public_path($menu->gambar));
            }

            $file = $request->file('gambar');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('gambar_menu');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $validated['gambar'] = 'gambar_menu/' . $filename;
        }

        $menu->update($validated);

        return response()->json(['message' => 'Menu berhasil diperbarui.']);
    }


    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Hapus file gambar dari public/gambar_menu
        if ($menu->gambar && file_exists(public_path($menu->gambar))) {
            File::delete(public_path($menu->gambar));
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
