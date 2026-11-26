@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="hero" style="padding-top: 80px;">
    <div class="container-fluid px-0">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('images/hero-1.jpg') }}" class="d-block w-100" alt="Event 1">
                    <div class="carousel-caption">
                        <h1>Wujudkan Event Impian Anda</h1>
                        <p>Bersama Dyacara, setiap momen menjadi tak terlupakan</p>
                        <a href="/contact" class="btn btn-primary btn-lg">Hubungi Kami</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/hero-2.jpg') }}" class="d-block w-100" alt="Event 2">
                    <div class="carousel-caption">
                        <h1>Profesional Event Organizer</h1>
                        <p>Melayani berbagai jenis event dengan pengalaman lebih dari 10 tahun</p>
                        <a href="/services" class="btn btn-primary btn-lg">Lihat Layanan</a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services py-5">
    <div class="container">
        <h2 class="text-center mb-5">Layanan Kami</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-ring fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Engagement Event</h5>
                        <p class="card-text">Mewujudkan momen pertunangan Anda dengan sentuhan elegansi dan keindahan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Gathering Event</h5>
                        <p class="card-text">Mengelola acara gathering Anda secara profesional dan berkesan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-birthday-cake fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Birthday Event</h5>
                        <p class="card-text">Menghadirkan perayaan ulang tahun yang spektakuler dan tak terlupakan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-us bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Mengapa Memilih Kami?</h2>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <i class="fas fa-check-circle fa-3x text-primary mb-3"></i>
                <h5>Berpengalaman</h5>
                <p>5 tahun pengalaman dalam industri event organizer</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h5>Tim Profesional</h5>
                <p>Didukung oleh tim yang terlatih dan berpengalaman</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <i class="fas fa-star fa-3x text-primary mb-3"></i>
                <h5>Kualitas Terbaik</h5>
                <p>Mengutamakan kualitas dalam setiap detail acara</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                <h5>Kepuasan Klien</h5>
                <p>Komitmen untuk memberikan kepuasan maksimal</p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Events -->
<section class="recent-events py-5">
    <div class="container">
        <h2 class="text-center mb-5">Event Terbaru</h2>
        <div class="row">
            @forelse($latestEvents as $event)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" alt="{{ $event->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->title }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{!! $event->sanitized_description !!}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p>Belum ada event terbaru.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection