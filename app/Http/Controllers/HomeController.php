<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $latestEvents = Event::latest()->take(3)->get();
        } catch (\Throwable $e) {
            $latestEvents = collect();
        }

        return view('home', compact('latestEvents'));
    }
}