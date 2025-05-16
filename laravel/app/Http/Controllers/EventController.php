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
        // Ambil data event dari Node.js
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


}
