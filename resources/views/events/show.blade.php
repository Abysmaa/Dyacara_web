@extends('layouts.main')

@section('content')
<!-- Event Detail Header -->
<section class="event-detail-header bg-primary text-white py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>{{ $event->title }}</h1>
                <div class="d-flex align-items-center gap-2 mt-2">
                    @switch($event->category)
                        @case('engagement')
                            <span class="badge bg-primary px-3 py-2">Engagement</span>
                            @break
                        @case('gathering')
                            <span class="badge bg-success px-3 py-2">Gathering</span>
                            @break
                        @case('birthday')
                            <span class="badge bg-warning px-3 py-2">Birthday Party</span>
                            @break
                    @endswitch
                    <span class="text-white-50">|</span>
                    <span><i class="fas fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($event->event_date)->isoFormat('D MMMM Y') }}</span>
                    <span class="text-white-50">|</span>
                    <span><i class="fas fa-map-marker-alt me-2"></i>{{ $event->location }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Event Detail Content -->
<section class="event-detail-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Event Image -->
                <div class="event-image mb-4">
                    <img src="{{ $event->image ? Storage::disk('public')->url($event->image) : asset('images/event-default.jpg') }}" class="img-fluid rounded shadow" alt="{{ $event->title }}">
                </div>

                <!-- Event Description -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>Deskripsi Event</h4>
                        <div class="event-description mt-3">
                            {!! $event->sanitized_description !!}
                        </div>
                    </div>
                </div>

                <!-- Event Features -->
                <div class="card">
                    <div class="card-body">
                        <h4>Fitur Event</h4>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    @foreach ((array) $event->features as $feature)
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i> {{ trim($feature) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <!-- Contact Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tertarik dengan Event Ini?</h5>
                        <p class="text-muted">Hubungi kami untuk informasi lebih lanjut dan konsultasi gratis</p>
                        <a href="{{ route('contact.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-envelope me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
                @auth
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pesan Event Ini</h5>
                                @if ($event->status === 'completed' || $event->event_date->isBefore(today()))
                                    <div class="alert alert-secondary mb-0">Event ini sudah selesai dan tidak menerima pemesanan.</div>
                                @else
                                    <p class="text-muted">Permintaan pemesanan akan menggunakan tanggal event ini dan menunggu konfirmasi tim kami.</p>
                                    @error('event')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                    <form action="{{ route('events.book', $event) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="booking-phone" class="form-label">Nomor Telepon</label>
                                            <input id="booking-phone" name="phone" type="tel" class="form-control" value="{{ old('phone') }}" required maxlength="20">
                                            @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="booking-notes" class="form-label">Catatan (opsional)</label>
                                            <textarea id="booking-notes" name="notes" class="form-control" rows="3" maxlength="2000">{{ old('notes') }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Kirim Permintaan</button>
                                    </form>
                                @endif
                            </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Masuk untuk Memesan Event</a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection