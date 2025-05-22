<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanitEventController extends Controller
{
    public function index()
    {
        // Hanya return view, data diambil via JS dari Node.js
        return view('panit.event');
    }
}
