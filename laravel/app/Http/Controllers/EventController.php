<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class EventController extends Controller
{
    public function index()
    {
        // Ambil data event dari Node.js
        $response = Http::get('http://localhost:3000/api/events');

        if ($response->successful()) {
            $events = $response->json();
        } else {
            $events = [];
        }

        return view('member.dashboard', compact('events'));
    }
}
