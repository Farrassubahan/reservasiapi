<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $labels = ['Jan', 'Feb', 'Mar', 'Apr'];
        $data = [120, 90, 150, 200];

        return view('admin_dashboard', compact('labels', 'data'));
    }
}
?>
