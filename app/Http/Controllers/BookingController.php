<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with('event')
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }
}
