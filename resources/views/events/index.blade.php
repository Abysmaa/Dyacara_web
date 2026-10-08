@extends('layouts.main')

@section('content')
<!-- Events Header -->
<section class="events-header bg-primary text-white py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>Event Kami</h1>
                <p class="lead">Temukan berbagai event menarik yang kami selenggarakan</p>
            </div>
        </div>
    </div>
</section>

<!-- Events Filter -->
<section class="events-filter py-4 bg-light">
    <div class="container">
        <form action="{{ route('events.search') }}" method="GET" id="filterForm" class="mb-0">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select shadow-sm" name="category" id="categoryFilter">
                        <option value="">Semua Kategori</option>
                        <option value="engagement" {{ request('category') == 'engagement' ? 'selected' : '' }}>Engagement</option>
                        <option value="gathering" {{ request('category') == 'gathering' ? 'selected' : '' }}>Gathering</option>
                        <option value="birthday" {{ request('category') == 'birthday' ? 'selected' : '' }}>Birthday Party</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select shadow-sm" name="location" id="locationFilter">
                        <option value="">Semua Lokasi</option>
                        <option value="jakarta" {{ request('location') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="depok" {{ request('location') == 'depok' ? 'selected' : '' }}>Depok</option>
                        <option value="bogor" {{ request('location') == 'bogor' ? 'selected' : '' }}>Bogor</option>
                        <option value="serpong" {{ request('location') == 'serpong' ? 'selected' : '' }}>Serpong</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control shadow-sm" name="date" id="dateFilter" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <input type="search" class="form-control shadow-sm" name="search" placeholder="Cari event" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm" id="filterButton">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Events List -->
<section class="events-list py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($events as $event)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm transition-card">
                    @if($event->image)
                        <img src="{{ Storage::disk('public')->url($event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/event-default.jpg') }}" class="card-img-top" alt="Default Event Image" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body d-flex flex-column">
                        @switch($event->category)
                            @case('engagement')
                                <div class="badge bg-primary px-3 py-2 mb-3 align-self-start">Engagement</div>
                                @break
                            @case('gathering')
                                <div class="badge bg-success px-3 py-2 mb-3 align-self-start">Gathering</div>
                                @break
                            @case('birthday')
                                <div class="badge bg-warning px-3 py-2 mb-3 align-self-start">Birthday Party</div>
                                @break
                        @endswitch
                        <h5 class="card-title fw-bold mb-3 text-truncate">{{ $event->title }}</h5>
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span class="d-flex align-items-center">
                                <i class="fas fa-calendar me-2"></i>
                                <span>{{ \Carbon\Carbon::parse($event->event_date)->isoFormat('D MMMM Y') }}</span>
                            </span>
                            <span class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span class="text-capitalize">{{ $event->location }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-primary w-100 py-2 fw-medium">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-4 d-block"></i>
                    <h5 class="fw-bold mb-3">Tidak ada event yang tersedia saat ini.</h5>
                    <p class="mb-0 text-secondary">Silakan coba filter lain atau kembali lagi nanti.</p>
                </div>
            </div>
            @endforelse
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $events->links() }}
        </div>
    </div>
</section>

<style>
.card {
    overflow: hidden;
}
.card-title {
    font-size: 1.25rem;
    color: #2d3748;
    height: 1.5em;
}
.card-text {
    color: #4a5568;
    font-size: 0.95rem;
}
.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
}
.transition-card {
    transition: all 0.3s ease;
}
.transition-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
.btn-outline-primary {
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.2s ease;
}
.btn-outline-primary:hover {
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const filterButton = document.getElementById('filterButton');
    const filters = document.querySelectorAll('#categoryFilter, #locationFilter, #dateFilter');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        filterButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';
        filterButton.disabled = true;
        this.submit();
    });

    filters.forEach(filter => {
        filter.addEventListener('change', () => form.submit());
    });
});
</script>
@endsection