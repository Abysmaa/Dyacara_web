<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(9);
        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function search(Request $request)
    {
        $filters = $request->validate([
            'category' => ['nullable', Rule::in(['engagement', 'gathering', 'birthday'])],
            'location' => ['nullable', 'string', 'max:255'],
            'date'     => ['nullable', 'date'],
            'search'   => ['nullable', 'string', 'max:255'],
        ]);

        $query = Event::query();

        $query
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->where('location', 'like', '%' . $location . '%'))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->whereDate('event_date', $date))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            });

        $events = $query->latest()->paginate(9)->withQueryString();
        return view('events.index', compact('events'));
    }

    public function book(Request $request, Event $event)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^[0-9\s\-\+\(\)]+$/', 'min:10', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($event, $request, $validated): void {
            $lockedEvent = Event::query()
                ->whereKey($event->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedEvent->status === EventStatus::Completed
                || $lockedEvent->event_date->isBefore(today())
            ) {
                throw ValidationException::withMessages([
                    'event' => 'Event yang sudah selesai atau lewat tanggal tidak dapat dipesan.',
                ]);
            }

            $existingBooking = Booking::query()
                ->where('event_id', $lockedEvent->id)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->first();

            if (in_array($existingBooking?->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)) {
                throw ValidationException::withMessages([
                    'event' => 'Anda sudah memiliki permintaan pemesanan aktif untuk event ini.',
                ]);
            }

            $bookingData = [
                'event_id' => $lockedEvent->id,
                'user_id'  => $request->user()->id,
                'name'     => $request->user()->name,
                'email'    => $request->user()->email,
                'phone'    => $validated['phone'],
                'date'     => $lockedEvent->event_date->copy()->startOfDay(),
                'status'   => BookingStatus::Pending,
                'notes'    => $validated['notes'] ?? null,
            ];

            if ($existingBooking) {
                $existingBooking->update($bookingData);
            } else {
                Booking::create($bookingData);
            }
        });

        return redirect()->route('events.show', $event)->with('success', 'Permintaan pemesanan berhasil dikirim.');
    }
}