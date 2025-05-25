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

    public function edit($id)
    {
        // Pass event id to the view, data will be fetched by JS from Node.js
        return view('panit.editEvent', ['eventId' => $id]);
    }
}
