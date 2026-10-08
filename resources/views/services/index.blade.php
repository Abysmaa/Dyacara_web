@extends('layouts.main')

@section('content')
<!-- Services Header -->
<section class="services-header bg-primary text-white py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>Layanan Kami</h1>
                <p class="lead">Solusi lengkap untuk setiap event spesial Anda</p>
            </div>
        </div>
    </div>
</section>

<!-- Services List -->
<section class="services-list py-5">
    <div class="container">
        <div class="row">
            @forelse($services as $service)
            <!-- Service Card -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->image ? asset('storage/' . $service->image) : ($loop->index == 0 ? asset('images/event-1.jpg') : ($loop->index == 1 ? asset('images/event-2.jpg') : asset('images/event-3.jpg'))) }}" 
                         class="card-img-top" alt="{{ $service->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text">{{ $service->seo_description }}</p>
                        <ul class="list-unstyled">
                            @if(is_array($service->features))
                                @foreach($service->features as $feature)
                                <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                @endforeach
                            @endif
                        </ul>
                        <div class="price-tag mt-3">
                            <h4 class="text-primary">{{ $service->formatted_price }}</h4>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="{{ route('payment.show', ['service' => $service->slug]) }}"
                           class="btn btn-primary w-100 mb-2">Pesan Sekarang</a>
                        <a href="/contact" class="btn btn-outline-primary w-100">Hubungi Kami</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h4>Belum ada layanan tersedia</h4>
                    <p>Silakan kembali lagi nanti.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-us py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Mengapa Memilih Kami?</h2>
            <p class="lead">Kami berkomitmen memberikan layanan terbaik untuk event Anda</p>
        </div>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <i class="fas fa-star fa-3x mb-3"></i>
                <h4>Berpengalaman</h4>
                <p>Tim profesional dengan pengalaman bertahun-tahun</p>
            </div>
            <div class="col-md-3 mb-4">
                <i class="fas fa-hand-holding-heart fa-3x mb-3"></i>
                <h4>Pelayanan Prima</h4>
                <p>Mengutamakan kepuasan dan kenyamanan klien</p>
            </div>
            <div class="col-md-3 mb-4">
                <i class="fas fa-gem fa-3x mb-3"></i>
                <h4>Kualitas Terbaik</h4>
                <p>Menggunakan vendor dan material berkualitas</p>
            </div>
            <div class="col-md-3 mb-4">
                <i class="fas fa-coins fa-3x mb-3"></i>
                <h4>Harga Bersaing</h4>
                <p>Penawaran harga yang kompetitif dan transparan</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta py-5">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <h2>Siap Mewujudkan Event Impian Anda?</h2>
                <p class="lead mb-4">Konsultasikan kebutuhan event Anda dengan tim kami</p>
                <a href="/contact" class="btn btn-primary btn-lg">Hubungi Kami Sekarang</a>
            </div>
        </div>
    </div>
</section>
@endsection
