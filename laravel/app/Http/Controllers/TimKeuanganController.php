<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimKeuanganController extends Controller
{
    public function edit($id)
    {
        // Ambil data user dari API Node.js
        $response = @file_get_contents("http://localhost:3000/api/users/keuangan/" . $id);
        if ($response === false) {
            abort(404, 'User tidak ditemukan');
        }
        $user = json_decode($response);
        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }
        return view('admin.editTimKeuangan', compact('user'));
    }
}
