<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanitiaController extends Controller
{
    public function edit($id)
    {
        // Ambil data user panitia dari API Node.js
        $response = @file_get_contents("http://localhost:3000/api/users/panitia/" . $id);
        if ($response === false) {
            abort(404, 'User tidak ditemukan');
        }
        $user = json_decode($response);
        if (!$user) {
            abort(404, 'User tidak ditemukan');
        }
        return view('admin.editPanitia', compact('user'));
    }
}
