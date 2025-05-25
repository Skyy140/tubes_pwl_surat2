<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class EventController extends Controller
{
    // public function index(){
    //     // Ambil data event dari Node.js
    //     $response = Http::get('http://localhost:3000/api/events');

    //     if ($response->successful()) {
    //         $events = $response->json();
    //     } else {
    //         $events = [];
    //     }
    //     //dd($events);

    //     return view('member.dashboard', compact('events'));
    // }
    public function index()
    {
        $responseEvents = Http::get('http://localhost:3000/api/events');
        $responseCategories = Http::get('http://localhost:3000/api/events/categories/all');

        if ($responseEvents->successful()) {
            $events = $responseEvents->json();
        } else {
            $events = [];
        }

        if ($responseCategories->successful()) {
            $categories = $responseCategories->json();
        } else {
            $categories = [];
        }
        // dd($events);

        return view('member.dashboard', compact('events', 'categories'));
    }

    public function show($id)
    {
        $response = Http::get("http://localhost:3000/api/events/$id");
        $event = $response->json();

        return view('member.detail_event', compact('event'));
    }

    public function formDaftar($id)
    {
        $response = Http::get("http://localhost:3000/api/events/$id");
        $daftar_event = $response->json();

        return view('member.daftar_event', compact('daftar_event'));
    }

    public function eventSaya()
    {
        $token = session('token');
        if (!$token) {
            return redirect('/login')->with('error', 'Harap login terlebih dahulu.');
        }

        $decoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))), true);
        $userId = $decoded['id'];

        $response = Http::withToken($token)->get("http://localhost:3000/api/events/registrasi/user/$userId");

        $registrations = $response->successful() ? $response->json() : [];

        return view('member.list_event', compact('registrations'));
    }
}
