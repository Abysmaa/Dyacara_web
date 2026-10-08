@extends('layouts.main')

@section('content')
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1>Pemesanan Saya</h1>
                <p class="text-muted mb-0">Pantau permintaan dan konfirmasi event Anda.</p>
            </div>
            <a href="{{ route('events.index') }}" class="btn btn-primary">Jelajahi Event</a>
        </div>

        @forelse ($bookings as $booking)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <h2 class="h5 mb-1">
                                @if ($booking->event)
                                    <a href="{{ route('events.show', $booking->event) }}" class="text-decoration-none">{{ $booking->event->title }}</a>
                                @else
                                    Event tidak lagi tersedia
                                @endif
                            </h2>
                            <small class="text-muted">Diminta {{ $booking->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted">Tanggal event</span>
                            <div class="fw-semibold">{{ $booking->date->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="badge {{ match ($booking->status) {
                                'confirmed' => 'bg-success',
                                'rejected' => 'bg-danger',
                                'completed' => 'bg-secondary',
                                default => 'bg-warning text-dark',
                            } }}">
                                {{ match ($booking->status) {
                                    'confirmed' => 'Dikonfirmasi',
                                    'rejected' => 'Ditolak',
                                    'completed' => 'Selesai',
                                    default => 'Menunggu Konfirmasi',
                                } }}
                            </span>
                        </div>
                        <div class="col-md-2 text-md-end">
                            @if ($booking->notes)
                                <span class="text-muted" title="{{ $booking->notes }}">Ada catatan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Anda belum memiliki pemesanan event.
                <a href="{{ route('events.index') }}" class="alert-link">Jelajahi event kami</a>.
            </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
            {{ $bookings->links() }}
        </div>
    </div>
</section>
@endsection
